<?php

namespace app\controllers;

use app\components\object\ClassType;
use app\models\Doc;
use app\models\Guide;
use app\models\search\SearchActiveRecord;
use Yii;
use yii\filters\HttpCache;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class GuideController extends BaseController
{
    public $sectionTitle = 'The Definitive Guide to Yii';
    public $searchScope = SearchActiveRecord::SEARCH_GUIDE;


    public function actionIndex($version, $language, $type = 'guide')
    {
        // normalize language, old yii 1.1 docs have _ in locale
        $normalizedLanguage = strtolower(str_replace('_', '-', $language));
        $guide = Guide::load($version, $normalizedLanguage, $type === 'blog' ? 'blogtut' : $type);
        if ($guide) {
            if ($normalizedLanguage !== $language) {
                $this->redirect(['index', 'language' => $normalizedLanguage, 'version' => $version, 'type' => $type]);
            }
            $this->sectionTitle = $guide->title;
            return $this->render('index', ['guide' => $guide]);
        }

        throw new NotFoundHttpException('The requested page was not found.');
    }

    public function actionView($section, $version, $language, $type = 'guide')
    {
        $normalizedLanguage = strtolower(str_replace('_', '-', $language));
        $guide = Guide::load($version, $normalizedLanguage, $type === 'blog' ? 'blogtut' : $type);
        if ($guide && $normalizedLanguage !== $language) {
            $this->redirect(['view', 'language' => $normalizedLanguage, 'version' => $version, 'section' => $section, 'type' => $type]);
        }

        $this->sectionTitle = $guide->title;

        if ($guide && ($section = $guide->loadSection($section))) {
            $urlParams = ['type' => $guide->typeUrlName, 'version' => $guide->version, 'language' => $guide->language, 'section' => $section->name];
            $docUrl = Url::to(array_merge(['guide/view'], $urlParams));
            $doc = Doc::getObject(ClassType::GUIDE, implode("/", $urlParams), $docUrl, $section->getPageTitle());

            return $this->render('view', [
                'guide' => $guide,
                'section' => $section,
                'missingTranslation' => $section->missingTranslation,
                'type' => $type,
                'doc' => $doc,
            ]);
        }

        throw new NotFoundHttpException('The requested page was not found.');
    }

    public function actionImage($image, $version, $language, $type = 'guide')
    {
        $file = Guide::findImage($image, $version, $language, $type === 'blog' ? 'blogtut' : $type);
        if ($file === false && $language !== 'en') {
            $file = Guide::findImage($image, $version, 'en', $type === 'blog' ? 'blogtut' : $type);
        }
        if ($file === false) {
            throw new NotFoundHttpException("The requested image was not found: $image");
        }

        $cache = new HttpCache([
            'cacheControlHeader' => 'public, max-age=86400',
            'lastModified' => function ($file) {
                return filemtime($file);
            },
        ]);
        if ($cache->beforeAction(null)) {
            Yii::$app->response->sendFile($file, null, ['inline' => true]);
        }
    }

    public function actionDownload($version, $language, $format)
    {
        $guide = Guide::load($version, $language);
        if ($guide && ($file = $guide->getDownloadFile($format)) !== false) {

            $cache = new HttpCache([
                'cacheControlHeader' => 'public, max-age=86400',
                'lastModified' => function ($file) {
                    return filemtime($file); // TODO does this work?
                },
            ]);
            if ($cache->beforeAction(null)) {
                return Yii::$app->response->sendFile($file['file'], $file['name']);
            }
        }

        throw new NotFoundHttpException('The requested page was not found.');
    }

    /**
     * Redirection for short urls to default version/language
     */
    public function actionEntry($version = null, $language = null)
    {
        // choose the latest version
        if ($version === null) {
            $versions = array_keys(Yii::$app->params['guide.versions']);
            arsort($versions, SORT_NATURAL);
            $version = array_shift($versions);
        }

        // negotiate language from browser preference
        if ($language === null) {
            $languages = array_keys(Yii::$app->params['guide.versions'][$version]);
            $language = Yii::$app->request->getPreferredLanguage($languages);
        }

        return $this->redirect(['index', 'version' => $version, 'language' => $language, 'type' => 'guide']);
    }

    /**
     * Redirection for short urls to default version/language
     */
    public function actionBlogEntry($version = null, $language = null)
    {
        // choose the latest version
        if ($version === null) {
            $versions = array_keys(Yii::$app->params['blogtut.versions']);
            arsort($versions, SORT_NATURAL);
            $version = array_shift($versions);
        }

        // negotiate language from browser preference
        if ($language === null) {
            $languages = array_keys(Yii::$app->params['blogtut.versions'][$version]);
            $language = Yii::$app->request->getPreferredLanguage($languages);
        }

        return $this->redirect(['index', 'version' => $version, 'language' => $language, 'type' => 'blog']);
    }

    /**
     * This action redirects old urls http://www.yiiframework.com/doc-2.0/guide-*.html to the new location.
     */
    public function actionRedirect($section)
    {
        if ($section === 'README' || $section === 'index') {
            return $this->redirect(['index', 'version' => '2.0', 'language' => 'en', 'type' => 'guide'], 301); // Moved Permanently
        }

        $guide = Guide::load('2.0', 'en');
        if ($guide && ($section = $guide->loadSection($section))) {
            return $this->redirect(['view', 'version' => '2.0', 'section' => $section->name, 'language' => 'en', 'type' => 'guide'], 301); // Moved Permanently
        }

        throw new NotFoundHttpException('The requested page was not found.');
    }
}
