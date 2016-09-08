<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * This is the model class for table "user_badges".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $badge_id
 * @property integer $progress
 * @property string $create_time
 * @property string $complete_time
 * @property string $message
 * @property integer $notified
 *
 * @property Badge $badge
 * @property User $user
 */
class UserBadge extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_badges';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'badge_id', 'create_time'], 'required'],
            [['user_id', 'badge_id', 'progress', 'notified'], 'integer'],
            [['create_time', 'complete_time'], 'safe'],
            [['message'], 'string', 'max' => 255],
            [['badge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Badge::className(), 'targetAttribute' => ['badge_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'badge_id' => 'Badge ID',
            'progress' => 'Progress',
            'create_time' => 'Create Time',
            'complete_time' => 'Complete Time',
            'message' => 'Message',
            'notified' => 'Notified',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBadge()
    {
        return $this->hasOne(Badge::className(), ['id' => 'badge_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (empty($this->create_time)) {
            $this->create_time = new Expression('NOW()');
        }
        if (empty($this->complete_time) && $this->progress >= 100) {
            $this->complete_time = new Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->progress >= 100) {
            $this->badge->updateCounters(['achieved' => 1]);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param integer $user_id
     * @param integer $badge_id
     * @return static
     */
    public static function findOrCreate($user_id, $badge_id)
    {
        $badge = static::findOne(['user_id' => $user_id, 'badge_id' => $badge_id]);
        if ($badge === null) {
            $badge = new static();
            $badge->user_id = $user_id;
            $badge->badge_id = $badge_id;
        }
        return $badge;
    }

    public static function listUsers(Badge $badge)
    {
        $query = static::find()
            ->where(['badge_id' => $badge->id])->andWhere('complete_time IS NOT NULL')
            ->with('user')
            ->orderBy('complete_time DESC');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
    }


    public static function countUsers(Badge $badge = null)
    {
        if ($badge !== null) {
            $query = static::find()->where(['badge_id' => $badge->id])->andWhere('complete_time IS NOT NULL');
            return $query->count();
        } else {
            $query = static::find()
                ->select(['id' => 'badge_id', 'total' => 'COUNT(badge_id)'])
                ->where('complete_time IS NOT NULL')
                ->groupBy('badge_id');
            $counts = [];
            foreach($query->asArray()->all() as $row) {
                $counts[$row['id']] = intval($row['total']);
            }
            return $counts;
        }
    }
}
