<?php

namespace app\models;

use Yii;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "rating".
 *
 * @property integer $user_id
 * @property string $object_type
 * @property integer $object_id
 * @property integer $rating
 * @property string $created_at
 *
 * @property User $user
 */
class Rating extends \yii\db\ActiveRecord
{
    public static $modelClasses = ['Comment', 'Wiki'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rating}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Returns the vote counts for the specified model.
     * @param ActiveRecord $model the specified model
     * @return array the vote counts (total votes, up votes)
     */
    public static function getVotes($model)
    {
        $row = static::find()
            ->select(['total_votes' => 'COUNT(*)', 'up_votes' => 'SUM(rating)'])
            ->where(['object_type' => $model->formName(), 'object_id' => (int)$model->primaryKey])
            ->asArray()
            ->one();

        return empty($row) ? [0, 0] : [(int)$row['total_votes'], (int)$row['up_votes']];
    }

    /**
     * Casts a vote to the specified content object.
     * @param ActiveRecord $model the type of the content object
     * @param integer $userID the user ID
     * @param integer $vote the vote (1 means up vote, 0 means down vote)
     * @return array the updated vote information of the content object (total votes, up votes). False if the content object is invalid.
     */
    public static function castVote($model, $userID, $vote)
    {
        /** @var $rating Rating */
        $rating = static::findOne([
            'object_type' => $model->formName(),
            'object_id' => (int)$model->primaryKey,
            'user_id' => $userID,
        ]);

        $vote = $vote ? 1 : 0;

        if($rating===null)
        {
            $rating = new static;
            $rating->object_type = $model->formName();
            $rating->object_id = (int)$model->primaryKey;
            $rating->user_id = $userID;
            $rating->rating = $vote;
            $rating->save(false);
        }
        else if($rating->rating!=$vote)
        {
            $rating->rating=$vote;
            $rating->save(false);
        }
        else
            // TODO ensure model has these properties
            return array($model->total_votes, $model->up_votes);

        $votes = static::getVotes($model);

        $model->updateAttributes([
            'total_votes' => $votes[0],
            'up_votes' => $votes[1],
            'rating' => static::wilsonLowerInterval($votes[1], $votes[0]),
        ]);

        return $votes;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = new Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Badge::check();
    }

    /**
     * Calculate the Wilson lower bound score based on
     * http://en.wikipedia.org/wiki/Binomial_proportion_confidence_interval#Wilson_score_interval
     * @param int $s number of successful trials
     * @param int $n number of all trials
     * @param string $alpha value, accepts only '0.01', '0.05' or '0.1'
     * @return float Wilson score interval
     * @throws \yii\base\InvalidParamException
     */
    public static function wilsonLowerInterval($s, $n, $alpha='0.05')
    {
        if($n===0)
            return 0;
        $p=$s/$n;
        $z=0;
        if($alpha==='0.01')
            $z=2.5758;
        if($alpha==='0.05')
            $z=1.96;
        if($alpha==='0.1')
            $z=1.6449;
        if($z===0)
            throw new InvalidParamException('Alpha must be 0.1 or 0.05');
        $z2 = $z*$z;
        $v = ($p + $z2/(2.0*$n) - $z * sqrt( $p*(1.0 - $p)/$n + $z2/(4*$n*$n) )) / (1.0 + $z2/$n);
        return $v<0 ? 0 : $v;
    }
}
