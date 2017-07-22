<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
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
class WikiCategory extends BaseCategory
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wiki_categories';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWikis()
    {
        return $this->hasMany(Wiki::className(), ['category_id' => 'id']);
    }

    /**
     * @return string
     */
    protected static function getObjectRelationName()
    {
        return 'wikis';
    }
}
