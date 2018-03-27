<?php

namespace app\models;

use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%report}}".
 *
 * @property int $id
 * @property int $status
 * @property string $object_type
 * @property int $object_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property int $creator_id
 * @property int $updater_id
 *
 * @property User $updater
 * @property User $creator
 */
class Report extends ActiveRecord
{
    const STATUS_OPEN = 10;
    const STATUS_DONE = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'object_id'], 'integer'],
            [['object_type', 'object_id', 'content'], 'required'],
            [['content'], 'string'],
            [['object_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'updater_id',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'object_type' => 'Object Type',
            'object_id' => 'Object ID',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'creator_id' => 'Creator ID',
            'updater_id' => 'Updater ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updater_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    /**
     * @inheritdoc
     * @return ReportQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReportQuery(static::class);
    }
}
