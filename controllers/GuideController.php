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
        $guide = Guide::load($version, $language);
        if ($guide) {
            return $this->render('index', ['guide' => $guide]);
        } else {
            throw new NotFoundHttpException('The requested page was not found.');
        }
    }

    public function actionView($section, $version, $language)
    {
        $guide = Guide::load($version, $language);
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
