<?php

namespace app\controllers;

use app\models\Guide;
use Yii;
use yii\filters\HttpCache;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GuideController extends Controller
{
    public function actionIndex($version, $language)
    {
        // normalize language, old yii 1.1 docs have _ in locale
        $normalizedLanguage = strtolower(str_replace('_', '-', $language));
        $guide = Guide::load($version, $normalizedLanguage);
        if ($guide) {
            if ($normalizedLanguage !== $language) {
                $this->redirect(['index', 'language' => $normalizedLanguage, 'version' => $version]);
            }
            return $this->render('index', ['guide' => $guide]);
        } else {
            throw new NotFoundHttpException('The requested page was not found.');
        }
    }

    public function actionView($section, $version, $language)
    {
        $normalizedLanguage = strtolower(str_replace('_', '-', $language));
        $guide = Guide::load($version, $normalizedLanguage);
        if ($guide && $normalizedLanguage !== $language) {
            $this->redirect(['view', 'language' => $normalizedLanguage, 'version' => $version, 'section' => $section]);
        }

        if ($guide && ($section = $guide->loadSection($section))) {
            return $this->render('view', [
                'guide' => $guide,
                'section' => $section,
                'missingTranslation' => $section->missingTranslation,
            ]);
        } else {
            throw new NotFoundHttpException('The requested page was not found.');
        }
    }

    public function actionImage($image, $version, $language)
    {
        $file = Guide::findImage($image, $version, $language);
        if ($file === false && $language !== 'en') {
            $file = Guide::findImage($image, $version, 'en');
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

        return $this->redirect(['index', 'version' => $version, 'language' => $language]);
    }

    /**
     * This action redirects old urls http://www.yiiframework.com/doc-2.0/guide-*.html to the new location.
     */
    public function actionRedirect($section)
    {
        if ($section === 'README') {
            return $this->redirect(['index', 'version' => '2.0', 'language' => 'en'], 301); // Moved Permanently
        }

        $guide = Guide::load('2.0', 'en');
        if ($guide && ($section = $guide->loadSection($section))) {
            return $this->redirect(['view', 'version' => '2.0', 'section' => $section->name, 'language' => 'en'], 301); // Moved Permanently
        } else {
            throw new NotFoundHttpException('The requested page was not found.');
        }
    }
}
