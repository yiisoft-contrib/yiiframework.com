<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[News]].
 *
 * @see News
 */
class NewsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return News[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return News|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function latest()
    {
        return $this
            ->orderBy(['news_date' => SORT_DESC]);
    }

    public function published()
    {
        return $this
            ->andWhere(['status' => News::STATUS_PUBLISHED]);
    }
}
