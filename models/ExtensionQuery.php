<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Extension]].
 *
 * @method Extension[]|array all($db = null)
 * @method Extension|array|null one($db = null)
 *
 * @see Extension
 */
class ExtensionQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function latest()
    {
        return $this
            ->andWhere(['status' => Extension::STATUS_PUBLISHED])
            ->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Extension::STATUS_PUBLISHED]);
    }

    /**
     * Select only Official Extensions
     *
     * @return $this
     */
    public function official()
    {
        return $this->andWhere("name LIKE 'yiisoft/yii2-%'");
    }

    /**
     * Exclude Official Extensions
     *
     * @return $this
     */
    public function excludeOfficial()
    {
        return $this->andWhere("name NOT LIKE 'yiisoft/yii2-%'");
    }

}
