<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * Class UserQuery
 *
 * @see User
 */
class UserQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => User::STATUS_ACTIVE]);
    }
}
