<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
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

    /**
     * @inheritdoc
     *
     * @return ContentShare[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     *
     * @return ContentShare|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
