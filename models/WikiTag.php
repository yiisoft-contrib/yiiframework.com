<?php

namespace app\models;

use Yii;
use app\components\SluggableBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "wiki_tags".
 *
 * @property integer $id
 * @property integer $frequency
 * @property string $name
 * @property string $slug
 *
 * @property Wiki[] $wikis
 */
class WikiTag extends ActiveRecord
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
        return '{{%wiki_tags}}';
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
    public function getWikis()
    {
        return $this->hasMany(Wiki::class, ['id' => 'wiki_id'])
            ->viaTable('wiki2wiki_tags', ['wiki_tag_id' => 'id']);
    }
}
