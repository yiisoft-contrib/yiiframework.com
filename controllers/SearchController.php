<?php

namespace app\controllers;

use app\models\SearchApiType;
use app\models\SearchActiveRecord;
use Yii;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\elasticsearch\Command;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class SearchController extends Controller
{
    public $searchQuery;

    public function actionGlobal($q, $version = null, $language = null)
    {
        if (!in_array($version, $this->getVersions())) {
            $version = null;
        }
        if (!in_array($language, array_keys($this->getLanguages()))) {
            $language = null;
        }

        $results = new ActiveDataProvider([
            'query' => SearchActiveRecord::search($q, $version, $language),
            'key' => 'primaryKey',
            'sort' => false,
        ]);

        $this->searchQuery = $q;

        return $this->render('results', [
            'results' => $results,
            'queryString' => $q,
            'version' => $version,
            'language' => $language,
        ]);
    }

    public function actionSuggest($q, $version = null, $language = null)
    {
        if (!in_array($version, $this->getVersions())) {
            $version = null;
        }
        if (!in_array($language, array_keys($this->getLanguages()))) {
            $language = null;
        }

        /** @var Command $command */
        $command = Yii::$app->elasticsearch->createCommand();
        $command->index = SearchActiveRecord::index();
        $result = $command->suggest(['my-suggestion' => ['text' => $q, 'term' => ['field' => 'body']]]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$result) {
            return [];
        } else {
            return $result;
        }
    }

    public function getVersions()
    {
        $versions = Yii::$app->params['api.versions'];
        return array_combine($versions, $versions);
    }

    public function getLanguages()
    {
        $languages = [];
        foreach(Yii::$app->params['guide.versions'] as $version => $l) {
            $languages = array_merge($languages, $l);
        }
        return $languages;
    }
}
