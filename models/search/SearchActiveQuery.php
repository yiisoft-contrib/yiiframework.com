<?php
/**
 * Created by PhpStorm.
 * User: cebe
 * Date: 13.10.15
 * Time: 17:42
 */

namespace app\models\search;


use yii\elasticsearch\ActiveQuery;
use yii\elasticsearch\Command;
use yii\elasticsearch\Connection;

class SearchActiveQuery extends ActiveQuery
{
    public $indicesBoost;

    /**
     * Creates a DB command that can be used to execute this query.
     * @param Connection $db the database connection used to execute the query.
     * If this parameter is not given, the `elasticsearch` application component will be used.
     * @return Command the created DB command instance.
     */
    public function createCommand($db = null)
    {
        $command = parent::createCommand($db);

        if ($this->indicesBoost !== null) {
            $command->queryParts['indices_boost'] = $this->indicesBoost;
        }

        return $command;
    }
} 