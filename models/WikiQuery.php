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
}