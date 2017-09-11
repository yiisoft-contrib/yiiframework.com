<?php

namespace app\models;

use yii\db\ActiveQuery;

class CommentQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Comment::STATUS_ACTIVE]);
    }

    /**
     * @param string $type
     * @param int $id
     * @return $this
     */
    public function forObject($type, $id)
    {
        return $this->andWhere(['object_type' => $type, 'object_id' => $id]);
    }

    /**
     * @param string $type
     * @param int $count
     * @return $this
     */
    public function recentComments($type, $count)
    {
        return $this->andWhere(['object_type' => $type])->orderBy(['created_at' => SORT_DESC])->limit($count);
    }
}
