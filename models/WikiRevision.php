<?php

namespace app\models;

use Yii;

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
            [['wiki_id', 'revision', 'title', 'content', 'category_id', 'memo'], 'required'],
            [['wiki_id', 'revision', 'category_id', 'updater_id'], 'integer'],
            [['content'], 'string'],
            [['updated_at'], 'safe'],
            [['title', 'memo'], 'string', 'max' => 255],
            [['updater_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updater_id' => 'id']],
            [['wiki_id'], 'exist', 'skipOnError' => true, 'targetClass' => Wiki::className(), 'targetAttribute' => ['wiki_id' => 'id']],
        ];
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
}
