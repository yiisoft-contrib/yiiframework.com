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
