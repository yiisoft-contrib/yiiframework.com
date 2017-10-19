<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tweet]].
 *
 * @see Tweet
 */
class TweetQuery extends \yii\db\ActiveQuery
{
    /**
     * @return $this
     */
    public function forPublishing()
    {
        return $this->andWhere(['status' => Tweet::STATUS_NEW]);
    }

    /**
     * @inheritdoc
     * @return Tweet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Tweet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
