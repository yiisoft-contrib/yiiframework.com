<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * Class WikiQuery
 *
 * @method Wiki[]|array all($db = null)
 * @method Wiki|array|null one($db = null)
 *
 * @see Wiki
 */
class WikiQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function latest()
        {
            return $this
                ->andWhere(['status' => Wiki::STATUS_PUBLISHED])
                ->orderBy(['created_at' => SORT_DESC]);
        }

    /**
     * @return $this
     */
    public function active()
    {
        return $this->andWhere(['status' => Wiki::STATUS_PUBLISHED]);
    }
}
