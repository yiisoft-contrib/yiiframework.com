<?php

namespace app\controllers;

use app\models\SearchApiType;
use app\models\SearchActiveRecord;
use Yii;
use yii\helpers\Url;
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
        $command->index = SearchActiveRecord::index() . '-en';
        $result = $command->suggest(['my-suggestion' => ['text' => $q, 'term' => ['field' => 'body']]]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$result) {
            return [];
        } else {
            return $result;
        }
    }

    public function actionAsYouType($q, $version = null, $language = null)
    {
        if (!in_array($version, $this->getVersions())) {
            $version = null;
        }
        if (!in_array($language, array_keys($this->getLanguages()))) {
            $language = null;
        }

        $query = SearchActiveRecord::searchAsYouType($q, $version, $language);
        $query->fields(['title', 'name', 'version', 'language', 'type']);
        $result = $query->search()['hits']['hits'];

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$result) {
            return [];
        } else {
            return array_values(array_map(function($r) {
                return [
                    'title' => $r->title,
                    'url' => Url::to($r->getUrl(), true),
                    'version' => $r->version,
                    'language' => $r->language,
                ];
            }, $result));
        }
    }

    public function getVersions()
    {
        $versions = Yii::$app->params['versions']['api'];
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
