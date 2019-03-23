<?php

namespace app\controllers;

use app\models\search\SearchActiveRecord;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Response;
use yii\data\ActiveDataProvider;


class SearchController extends BaseController
{
    public $searchQuery;

    public function actionGlobal($q = null, $version = null, $language = null, $type = null)
    {
        if (!in_array($version, $this->getVersions(), true)) {
            $version = null;
        }
        if (!array_key_exists($language, $this->getLanguages())) {
            $language = null;
        }
        if (!array_key_exists($type, $this->getTypes())) {
            $type = null;
        }
        // reset version an language restrictions for types that do not have the selection option
        if ($type === 'news') {
            $language = null;
            $version = null;
        } elseif (in_array($type, ['wiki', 'extension', 'api'], true)) {
            $language = null;
        }

        $this->searchQuery = $q;
        if (empty($q)) {
            $this->sectionTitle = 'Search';
            $this->headTitle = 'Search';
            $results = new ArrayDataProvider();
        } else {
            $this->sectionTitle = 'Search results';
            $this->headTitle = "Search results for \"$q\"";
            $results = new ActiveDataProvider(
                [
                    'query' => SearchActiveRecord::search($q, $version, $language, $type),
                    'key' => 'primaryKey',
                    'sort' => false,
                ]
            );
        }

        return $this->render(
            'results',
            [
                'results' => $results,
                'queryString' => $q,
                'version' => $version,
                'language' => $language,
                'type' => $type,
            ]
        );
    }

    public function actionSuggest($q, $version = null, $language = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = SearchActiveRecord::searchAsYouType($q, $version, $language);
        $results = $query->search()['suggest'];

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/json');

        $suggests = array_merge(
            $results['suggest-name'] ?? [],
            $results['suggest-title'] ?? []
        );

        $response = [
            'q' => $q,
            'suggestions' => [],
        ];
        $uniqueTitles = [];
        foreach ($suggests as $suggest) {
            foreach ($suggest['options'] as $result) {
                if (isset($uniqueTitles[$result['text']])) {
                    continue;
                }
                $uniqueTitles[$result['text']] = true;
                $response['suggestions'][] = [
                    'title' => $result['text'],
                    'url' => Url::toRoute(['search/global', 'q' => $result['text']]),
                ];
            }
        }

        return Json::encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

/*
    public function actionSuggest($q, $version = null, $language = null)
    {
        if (!in_array($version, $this->getVersions())) {
            $version = null;
        }
        if (!array_key_exists($language, $this->getLanguages())) {
            $language = null;
        }

        /** @var Command $command * /
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
*/

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

    public function getTypes()
    {
        return [
            SearchActiveRecord::SEARCH_GUIDE => 'Guide',
            SearchActiveRecord::SEARCH_API => 'API',
            SearchActiveRecord::SEARCH_EXTENSION => 'Extensions',
            SearchActiveRecord::SEARCH_WIKI => 'Wiki',
            SearchActiveRecord::SEARCH_NEWS => 'News',
        ];
    }

    public function actionOpensearchSuggest($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = SearchActiveRecord::searchAsYouType($q, null, null);
        $results = $query->search()['suggest'];

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/x-suggestions+json');

        $searchTerms = [];
        $descriptions = [];
        $queryURLs = [];

        $suggests = array_merge(
            $results['suggest-name'] ?? [],
            $results['suggest-title'] ?? []
        );

        $uniqueTitles = [];
        foreach ($suggests as $suggest) {
            foreach ($suggest['options'] as $result) {
                if (isset($uniqueTitles[$result['text']])) {
                    continue;
                }
                $uniqueTitles[$result['text']] = true;
                $searchTerms[] = $result['text'];
                $descriptions[] = '';
                $queryURLs[] = Url::toRoute(['search/global', 'q' => $result['text']]);
            }
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
