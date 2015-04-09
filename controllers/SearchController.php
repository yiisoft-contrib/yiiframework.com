<?php

namespace app\controllers;

use app\models\ApiType;
use app\models\SearchActiveRecord;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SearchController extends Controller
{
    public $searchQuery;

    public function actionGlobal($q)
    {
        $results = new ActiveDataProvider([
            'query' => SearchActiveRecord::search($q),
            'key' => 'primaryKey',
            'sort' => false,
        ]);

        $this->searchQuery = $q;

        return $this->render('results', [
            'results' => $results,
            'queryString' => $q,
        ]);
    }
}
