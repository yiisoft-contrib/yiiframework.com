<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * Class UserQuery
 *
 * @method User[]|array all($db = null)
 * @method User|array|null one($db = null)
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
