<?php

namespace app\controllers;

use Yii;
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
        $versions = Yii::$app->params['guide.versions'];
        if (!isset($versions[$version])) {
            throw new NotFoundHttpException("Version not found: $version");
        }
        if (!isset($versions[$version][$language])) {
            throw new NotFoundHttpException("Language not found: $language");
        }
        if (!preg_match('/^[\w\-]+$/', $section)) {
            throw new NotFoundHttpException("Section not found: $section");
        }

        $basePath = Yii::getAlias("@app/data/guide-$version");
        $file = "$basePath/$language/guide-$section.html";
        if (!is_file($file)) {
            // fall back to English version
            // todo: show prompt for helping translate the missing section
            $file = "$basePath/en/guide-$section.html";
            if (!is_file($file)) {
                throw new NotFoundHttpException("Section not found: $section");
            }
        }

        return $this->renderContent(file_get_contents($file));
    }
}
