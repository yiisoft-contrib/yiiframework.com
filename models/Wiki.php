<?php

namespace app\models;

use app\components\SluggableBehavior;
use dosamigos\taggable\Taggable;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "wiki".
 *
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property integer $category_id
 * @property integer $creator_id
 * @property integer $updater_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $yii_version
 *
 * @property integer $view_count
 *
 * @property User $updater
 * @property User $creator
 * @property WikiRevision[] $wikiRevisions
 */
class Wiki extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DELETED = 3;

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'creator_id',
                'updatedByAttribute' => 'updater_id',
            ],
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
                'attributes' => [
                    static::EVENT_BEFORE_INSERT => 'slug',
                    static::EVENT_BEFORE_UPDATE => 'slug',
                ],
            ],
            [
                'class' => Taggable::className(),
            ],
        ];
    }



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wiki}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'category_id'], 'required'],
            [['content'], 'string'],
            [['category_id'], 'exists', 'targetClass' => WikiCategory::class, 'targetAttribute' => 'id'],
            [['title'], 'string', 'max' => 255],
            [['yii_version'], 'string', 'max' => 5],

            [['tagNames'], 'safe'],
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
            'content' => 'Content',
            'category_id' => 'Category ID',
            'creator_id' => 'Creator ID',
            'updater_id' => 'Updater ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'yii_version' => 'Yii Version',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::className(), ['id' => 'updater_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWikiRevisions()
    {
        return $this->hasMany(WikiRevision::className(), ['wiki_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(WikiTag::className(), ['id' => 'wiki_tag_id'])
            ->viaTable('wiki2wiki_tags', ['wiki_id' => 'id']);
    }

}
