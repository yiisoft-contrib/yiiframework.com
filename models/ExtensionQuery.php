<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Extension]].
 *
 * @see Extension
 */
class ExtensionQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Extension::STATUS_PUBLISHED]);
    }

    /**
     * @inheritdoc
     * @return Extension[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Extension|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
