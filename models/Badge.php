<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "badges".
 *
 * @property integer $id
 * @property string $urlname
 * @property string $class
 * @property integer $achieved
 *
 * @property UserBadge[] $userBadges
 */
abstract class Badge extends ActiveRecord
{
    public $name;
    public $description;
    public $allowMultiple = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%badges}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['urlname', 'class'], 'required'],
            [['achieved'], 'integer'],
            [['urlname'], 'string', 'max' => 255],
            [['class'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'urlname' => 'Urlname',
            'class' => 'Class',
            'achieved' => 'Achieved',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserBadges()
    {
        return $this->hasMany(UserBadge::className(), ['badge_id' => 'id']);
    }

    /**
     * Process badges, this should be called from a command line task.
     */
    public function updateBadges($userIds)
    {
        foreach($this->getUnprocessedUsers($userIds) as $userId) {
            $userBadge = UserBadge::findOrCreate($userId, $this->id);
            if ($this->earned($userBadge)) {
                $userBadge->save();
            }
        }
    }

    /**
     * Add current user id as potential badge candidate
     */
    public static function check()
    {
        $user = Yii::$app->user->identity;
        if($user)
        {
            $userID = $user->id;
            static::addCandidate($userID);
        }
    }

    /**
     * Call to insert an user as potential badge candidate
     */
    public static function addCandidate($userID)
    {
        static::getDb()->createCommand()->insert('{{%badge_queue}}', ['user_id' => $userID])->execute();
    }

    /**
     * Override this method to calculate if this badge is earned.
     * @return bool True if user has earned this badge, false otherwise.
     */
    abstract public function earned(UserBadge $badge);

    /**
     * @return array list of user ids are candidates for badge processing
     */
    protected function getUnprocessedUsers($userIds)
    {
        if (!$this->allowMultiple && count($userIds) > 0)
        {
            $query = UserBadge::find()
                ->select('user_id')
                ->where(['badge_id' => $this->id])
                ->andWhere('complete_time IS NOT NULL')
                ->andWhere(['user_id' => $userIds]);
            $existing = $query->column();
            return array_diff($userIds, $existing);
        }
        return $userIds;
    }

    public static function instantiate($row)
    {
        if (isset($row['class'])) {
            $className = 'app\\models\\badges\\' . $row['class'];
            return new $className();
        }
        return new static;
    }
}
