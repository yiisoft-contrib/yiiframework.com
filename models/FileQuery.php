<?php

namespace app\models;

use yii\db\ActiveQuery;

class FileQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function latest()
    {
        return $this->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * @return $this
     */
    public function forObject($type, $id)
    {
        return $this->andWhere(['object_type' => $type, 'object_id' => $id]);
    }
}
