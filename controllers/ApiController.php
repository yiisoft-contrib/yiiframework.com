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
            throw new NotFoundHttpException('The requested version was not found.');
        }

        $file = Yii::getAlias("@app/data/api-$version/$section.html");
        if (!preg_match('/^[\w\-\.]+$/', $section) || !is_file($file)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        return $this->render('view', [
            'content' => file_get_contents($file),
            'section' => $section,
            'versions' => array_keys(Yii::$app->params['guide.versions']),
            'version' => $version,
        ]);
    }

    protected function getPageTitle($version, $language, $section)
    {
        $key = [
            __METHOD__,
            $version,
            $language,
            $section,
        ];
        if (($title = Yii::$app->cache->get($key)) !== false) {
            return $title;
        }

        $title = "The Definitive Guide for $version";

        $readmeFile = $this->findSection($version, $language, 'README');
        if ($readmeFile !== false) {
            if (preg_match('%<h1>\s*([^<>]+)\s*<%', file_get_contents($readmeFile), $matches)) {
                $title = $matches[1];
            }
        }

        if ($section !== 'README') {
            $sectionFile = $this->findSection($version, $language, $section);
            if ($sectionFile !== false) {
                if (preg_match('%<h1>\s*([^<>]+)\s*<%', file_get_contents($sectionFile), $matches)) {
                    $title = $matches[1] . ' | ' . $title;
                }
            }
        }

        Yii::$app->cache->set($key, $title, 86400);

        return $title;
    }
}
