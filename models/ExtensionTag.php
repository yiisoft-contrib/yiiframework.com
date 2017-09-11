<?php

namespace app\models;

use app\components\SluggableBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "extension_tags".
 *
 * @property integer $id
 * @property integer $frequency
 * @property string $name
 * @property string $slug
 *
 * @property Extension[] $extensions
 */
class ExtensionTag extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'attributes' => [static::EVENT_BEFORE_INSERT => 'slug'],
                'immutable' => true,
                'ensureUnique' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%extension_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'frequency' => 'Frequency',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getExtensions()
    {
        return $this->hasMany(Extension::class, ['id' => 'extension_id'])
            ->viaTable('extension2extension_tags', ['extension_tag_id' => 'id']);
    }
}
