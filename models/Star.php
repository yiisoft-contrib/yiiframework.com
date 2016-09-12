<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "star".
 *
 * @property integer $user_id
 * @property string $object_type
 * @property integer $object_id
 * @property integer $star
 * @property string $created_at
 */
class Star extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%star}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'object_type', 'object_id', 'created_at'], 'required'],
            [['user_id', 'object_id', 'star'], 'integer'],
            [['created_at'], 'safe'],
            [['object_type'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'object_type' => 'Object Type',
            'object_id' => 'Object ID',
            'star' => 'Star',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Returns the total stars for the specified model (how many users stared the object).
     * @param ActiveRecord $model the specified model
     * @return int the star counts
     */
    public static function getStarCount($model)
    {
        return static::find()
            ->where(['object_type' => $model->formName(), 'object_id' => (int)$model->primaryKey])
            ->sum('star');
    }

    /**
     * Casts a star to the specified content object.
     * @param ActiveRecord $model the type of the content object
     * @param integer $userID the user ID
     * @param integer $starValue the star value (1 means star, 0 means unstar, -1 means toggle)
     * @return int the updated star information of the content object. False if the content object is invalid.
     */
    public static function castStar($model, $userID, $starValue = -1)
    {
        /** @var $star Star */
        $star = static::findOne([
            'object_type' => $model->formName(),
            'object_id' => (int)$model->primaryKey,
            'user_id' => $userID,
        ]);

        if ($star === null)
        {
            $star = new static;
            $star->object_type = $model->formName();
            $star->object_id = (int)$model->primaryKey;
            $star->user_id = $userID;
            $star->star = 1;
            $star->save(false);
        }
        else
        {
            if(-1==$starValue)
            {
                $star->star = (0==$star->star) ? 1 : 0;
                $star->save(false);
            }
            else
            {
                /*
                 * If record already exist, allow only unstar (no automatic starring)
                 */
                if($starValue==0 && $star->star!=$starValue)
                {
                    $star->star = $starValue;
                    $star->save(false);
                }
            }
        }

        return $star->star;
    }

    /**
     * Returns the target models that the specified user is following
     * @param integer $userID the user ID
     * @return array list of models that the user is following
     */
    public static function getTargets($userID)
    {
        $models=[];
        // TODO implement
//        foreach(self::$contentClasses as $class)
//        {
//            $finder=CActiveRecord::model($class);
//            $title=$finder->hasAttribute('title') ? 'title' : 'name';
//            $models=array_merge($models, $finder->findAllBySql(
//                "SELECT id, $title FROM ".$finder->tableName()
//                . " INNER JOIN tbl_star ON object_type='$class' AND user_id=$userID AND star=1 AND object_id=id"
//                . " WHERE status=".self::STATUS_PUBLISHED
//                . " ORDER BY $title"
//            ));
//        }
        return $models;
    }

    /**
     * @param ActiveRecord $model
     * @return User[]
     */
    public function getFollowers($model)
    {
        $class = $model->formName();
        return User::find()
            ->where(['star' => 1, 'object_type' => $class, 'object_id' => (int)$model->primaryKey])
            ->all();
    }
}
