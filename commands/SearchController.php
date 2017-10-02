<?php

namespace app\commands;


use yii\console\Controller;
use Yii;
use yii\elasticsearch\Connection;
use yii\helpers\Console;

class SearchController  extends Controller
{
    /**
     * @var bool whether to show a progress bar.
     */
    public $progress = false;


    public function options($actionID)
    {
        if ($actionID === 'ranking') {
            return array_merge(parent::options($actionID), ['progress']);
        }
        return parent::options($actionID);
    }

    public function actionIndex()
    {
        $this->stdout("Search settings\n\n", Console::BOLD);

        /** @var $es Connection */
        $es = Yii::$app->elasticsearch;

        $this->stdout('Elasticsearch config: ' . print_r($es->nodes, true));

        $this->stdout('Elasticsearch indices: ' . print_r(array_keys($es->createCommand()->getIndexStats()['indices']), true));


    }

    /**
     * Rebuild search index.
     */
    public function actionRebuild()
    {

    }
}
