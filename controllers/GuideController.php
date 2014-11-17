<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GuideController extends Controller
{
    public function actionView($version, $language, $section)
    {
        // todo: language, list supported languages in a parameter
        $file = \Yii::getAlias('@app/data/doc-' . $version . '/guide-' . $section . '.html');
        if ($version !== '2.0' || !preg_match('/^[\w\-]+$/', $section) || !is_file($file)) {
            throw new NotFoundHttpException("The requested section does not exist: $version/$section");
        }
        return $this->renderContent(file_get_contents($file));
    }
}
