<?php

namespace app\controllers;

use app\components\packagist\Package;
use app\components\packagist\PackagistApi;
use app\models\SearchActiveRecord;
use Yii;
use yii\helpers\Json;
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
        if (!in_array($version, $this->getVersions(), true)) {
            $version = null;
        }
        if (!array_key_exists($language, $this->getLanguages())) {
            $language = null;
        }

        $results = new ActiveDataProvider(
            [
                'query' => SearchActiveRecord::search($q, $version, $language),
                'key' => 'primaryKey',
                'sort' => false,
            ]
        );

        $this->searchQuery = $q;

        return $this->render(
            'results',
            [
                'results' => $results,
                'queryString' => $q,
                'version' => $version,
                'language' => $language,
            ]
        );
    }

    public function actionSuggest($q, $version = null, $language = null)
    {
        if (!in_array($version, $this->getVersions())) {
            $version = null;
        }
        if (!array_key_exists($language, $this->getLanguages())) {
            $language = null;
        }

        /** @var Command $command */
        $command = Yii::$app->elasticsearch->createCommand();
        $command->index = SearchActiveRecord::index() . '-en';
        $result = $command->suggest(['my-suggestion' => ['text' => $q, 'term' => ['field' => 'body']]]);

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$result) {
            return [];
        }

        return $result;
    }

    public function actionAsYouType($q, $version = null, $language = null)
    {
        if (!in_array($version, $this->getVersions(), true)) {
            $version = null;
        }
        if (!array_key_exists($language, $this->getLanguages())) {
            $language = null;
        }

        $query = SearchActiveRecord::searchAsYouType($q, $version, $language);
        $query->fields(['title', 'name', 'version', 'language', 'type']);
        $result = $query->search()['hits']['hits'];

        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$result) {
            return [];
        }

        return array_values(
            array_map(
                function ($r) {
                    return [
                        'title' => $r->title,
                        'url' => Url::to($r->getUrl(), true),
                        'version' => $r->version,
                        'language' => $r->language,
                    ];
                },
                $result
            )
        );
    }

    /**
     * Extension search
     *
     * @param string $q query
     *
     * @return array
     */
    public function actionExtension($q)
    {
        $keyCache = 'search/extension__dataPackagist_' . md5(serialize([$q]));
        $packagistData = \Yii::$app->cache->get($keyCache);
        if ($packagistData === false) {
            $packagistData = (new PackagistApi())->search($q);
            \Yii::$app->cache->set($keyCache, $packagistData, Yii::$app->params['cache.extensions.search']);
        }

        $this->searchQuery = $q;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!$packagistData['packages']) {
            return [];
        }

        return array_values(
            array_map(
                function (Package $package) {
                    return [
                        'title' => $package->getName(),
                        'url' => $package->getUrl()
                    ];
                },
                $packagistData['packages']
            )
        );
    }

    public function getVersions()
    {
        $versions = Yii::$app->params['versions']['api'];
        return array_combine($versions, $versions);
    }

    public function getLanguages()
    {
        $languages = [];
        foreach (Yii::$app->params['guide.versions'] as $version => $l) {
            $languages = array_merge($languages, $l);
        }
        return $languages;
    }

    public function actionOpensearchSuggest($q)
    {
        $query = SearchActiveRecord::searchAsYouType($q, null, null);
        $query->fields(['title']);
        $results = $query->search()['hits']['hits'];

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/x-suggestions+json');

        $searchTerms = [];
        $descriptions = [];
        $queryURLs = [];

        foreach ($results as $result) {
            $searchTerms[] = $result->title;
            $descriptions[] = $result->title;
            $queryURLs[] = Url::toRoute(['search/global', 'q' => $result->title]);
        }

        return Json::encode([$q, $searchTerms, $descriptions, $queryURLs], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function actionOpensearchDescription()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml');
        return $this->renderPartial('opensearch-description');
    }
}
