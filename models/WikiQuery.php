<?php

namespace app\models;

use yii\db\ActiveQuery;

class WikiQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Wiki::STATUS_PUBLISHED]);
    }

    /**
     * @inheritdoc
     * @return Wiki[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Wiki|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
