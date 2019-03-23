<?php

namespace app\models;

/**
 * This is the model class for table "{{%extension_categories}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sequence
 *
 * @property Extension[] $extensions
 */
class ExtensionCategory extends BaseCategory
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%extension_categories}}';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtensions()
    {
        return $this->hasMany(Extension::class, ['category_id' => 'id']);
    }

    /**
     * @return string
     */
    protected static function getObjectRelationName()
    {
        return 'extensions';
    }
}
