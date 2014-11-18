<?php

namespace app\controllers;

use Yii;
use yii\filters\HttpCache;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GuideController extends Controller
{
    public function actionIndex($version, $language)
    {
        return $this->actionView($version, $language, 'README');
    }

    public function actionView($version, $language, $section)
    {
        $this->validateVersionAndLanguage($version, $language);

        $file = $this->findSection($version, $language, $section);
        if ($file === false && $language !== 'en') {
            $file = $this->findSection($version, 'en', $section);
        }
        if ($file === false) {
            throw new NotFoundHttpException("The requested guide section was not found.");
        }
        return $this->renderContent(file_get_contents($file));
    }

    public function actionImage($version, $language, $image)
    {
        $this->validateVersionAndLanguage($version, $language);

        $file = $this->findImage($version, $language, $image);
        if ($file === false && $language !== 'en') {
            $file = $this->findImage($version, 'en', $image);
        }
        if ($file !== false) {
            $cache = new HttpCache([
                'cacheControlHeader' => 'public, max-age=86400',
                'lastModified' => function ($file) {
                    return filemtime($file);
                },
            ]);
            if ($cache->beforeAction(null)) {
                Yii::$app->response->sendFile($file, null, ['inline' => true]);
            }
        } else {
            throw new NotFoundHttpException("The requested image was not found: $image");
        }
    }

    protected function findImage($version, $language, $image)
    {
        $file = Yii::getAlias("@app/data/guide-$version/$language/images/$image");
        return preg_match('/\.(png|jpg|gif)$/i', $image) && is_file($file) ? $file : false;
    }

    protected function findSection($version, $language, $section)
    {
        $file = Yii::getAlias("@app/data/guide-$version/$language/$section.html");
        return preg_match('/^[\w\-]+$/', $section) && is_file($file) ? $file : false;
    }

    protected function getGuidePath($version, $language)
    {
        return Yii::getAlias("@app/data/guide-$version/$language");
    }

    protected function validateVersionAndLanguage($version, $language)
    {
        $versions = Yii::$app->params['guide.versions'];
        if (isset($versions[$version]) && isset($versions[$version][$language])) {
            return true;
        } else {
            throw new NotFoundHttpException('The requested page was not found.');
        }
    }
}
