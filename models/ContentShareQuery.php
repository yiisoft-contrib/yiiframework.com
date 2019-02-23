<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * @method ContentShare[]|array all($db = null)
 * @method ContentShare|array|null one($db = null)
 *
 * @see ContentShare
 */
class ContentShareQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function forPublishing()
    {
        return $this->andWhere(['status_id' => [ContentShare::STATUS_NEW, ContentShare::STATUS_FAILED]]);
    }

}
