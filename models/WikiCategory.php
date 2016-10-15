<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "wiki_categories".
 *
 * @property integer $id
 * @property string $name
 * @property integer $sequence
 *
 * @property Wiki[] $wikis
 */
class WikiCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wiki_categories';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['sequence'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'sequence' => 'Sequence',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWikis()
    {
        return $this->hasMany(Wiki::className(), ['category_id' => 'id']);
    }

    public static function getSelectData()
    {
        return ArrayHelper::map(static::find()->orderBy(['name' => SORT_ASC])->asArray()->all(), 'id', 'name');
    }
}
