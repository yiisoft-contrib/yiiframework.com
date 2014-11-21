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
}
