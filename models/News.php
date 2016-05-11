<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $news_date
 * @property integer $image_id
 * @property string $content
 * @property string $created_at
 * @property string $updated_at
 * @property integer $creator_id
 * @property integer $updater_id
 * @property integer $status
 */
class News extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_DELETED = 4;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
//            [
//                //'class' => BlameableBehavior::class,
//                // TODO configure
//            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'attributes' => [static::EVENT_BEFORE_INSERT => 'slug'],
                'immutable' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // filters
            [['title'], 'trim'],

            // validators
            [['title', 'content', 'status', 'news_date'], 'required'],

            [['title'], 'string', 'max' => 128],
            [['content'], 'string'],

            ['status', 'in', 'range' => array_keys(static::getStatusList())],

            [['news_date'], 'date', 'timestampAttribute' => 'news_date', 'timestampAttributeFormat' => 'php:Y-m-d'],

        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'news_date' => 'News Date',
            'image_id' => 'Image ID',
            'content' => 'Content',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'creator_id' => 'Creator ID',
            'updater_id' => 'Updater ID',
            'status' => 'Status',
        ];
    }

    public function getTeaser()
    {
        return reset(explode("\n\n", $this->content));
    }

    /**
     * @inheritdoc
     * @return NewsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NewsQuery(get_called_class());
    }
}
