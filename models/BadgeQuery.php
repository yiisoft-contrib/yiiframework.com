<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * Class BadgeQuery
 *
 * @see Badge
 */
class BadgeQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['active' => 1]);
    }
}
