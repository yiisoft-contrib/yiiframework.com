<?php

namespace app\models;

use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "wiki_revision".
 *
 * @property integer $wiki_id
 * @property integer $revision
 * @property string $title
 * @property string $content
 * @property integer $category_id
 * @property string $memo
 * @property integer $updater_id
 * @property string $updated_at
 *
 * @property User $updater
 * @property Wiki $wiki
 */
class WikiRevision extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
                'createdAtAttribute' => false,
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => false,
                'updatedByAttribute' => 'updater_id',
            ],
            // TODO store tags
//            'tagable' => [
//                'class' => Taggable::className(),
//            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wiki_revision}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'category_id', 'memo'], 'required'],
            [['content'], 'string'],
            [['title', 'memo'], 'string', 'max' => 255],
        ];
    }


    public function scenarios()
    {
        return [
            'create' => ['title', 'content', 'category_id', 'memo']
        ];
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            throw new InvalidCallException('Updating a Wiki Revision is not allowed!');
        }

        if ($insert && $this->revision === null) {
            $this->revision = (int) static::find()->where(['wiki_id' => $this->wiki_id])->max('revision');
            $this->revision++;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'wiki_id' => 'Wiki ID',
            'revision' => 'Revision',
            'title' => 'Title',
            'content' => 'Content',
            'category_id' => 'Category ID',
            'memo' => 'Memo',
            'updater_id' => 'Updater ID',
            'updated_at' => 'Updated At',
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
    public function getWiki()
    {
        return $this->hasOne(Wiki::className(), ['id' => 'wiki_id']);
    }

    public static function diff(WikiRevision $a, WikiRevision $b, $attribute)
    {
        $diff = new \Diff(explode("\n", $a->$attribute), explode("\n", $b->$attribute));
        return $diff;
    }
}
