<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ApiController extends Controller
{
    public function actionIndex($version)
    {
        return $this->actionView($version, 'index');
    }

    public function actionView($version, $section)
    {
        $versions = Yii::$app->params['api.versions'];
        if (!in_array($version, $versions)) {
            // TODO make nicer error page
            throw new NotFoundHttpException('The requested version was not found.');
        }

        $title = '';
        $packages = [];
        if ($version[0] === '1') {
            $file = Yii::getAlias("@app/data/api-$version/api/$section.html");
            $packages = unserialize(file_get_contents(Yii::getAlias("@app/data/api-$version/api/packages.txt")));
            $view = 'view1x';
        } else {
            $file = Yii::getAlias("@app/data/api-$version/$section.html");
            $view = 'view2x';
            $titles = require(Yii::getAlias("@app/data/api-$version/titles.php"));
            if (isset($titles[$section . '.html'])) {
                $title = $titles[$section . '.html'];
            }
        }
        if (!preg_match('/^[\w\-]+$/', $section) || !is_file($file)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        return $this->render($view, [
            'content' => file_get_contents($file),
            'section' => $section,
            'versions' => Yii::$app->params['api.versions'],
            'version' => $version,
            'title' => $title,
            'packages' => $packages,
        ]);
    }

    /**
     * This action redirects old urls http://www.yiiframework.com/doc-2.0/*.html to the new location.
     */
    public function actionRedirect($section)
    {
        $file = Yii::getAlias("@app/data/api-2.0/$section.html");
        if (!preg_match('/^[\w\-]+$/', $section) || !is_file($file)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }
        return $this->redirect(['view', 'version' => '2.0', 'section' => $section], 301); // Moved Permanently
    }
}
