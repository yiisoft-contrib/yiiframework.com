<?php

namespace app\controllers;

use app\apidoc\ApiRenderer;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnsupportedMediaTypeHttpException;

/**
 * ApiController provides the framework API documentation
 *
 * API documentation is provided in HTML format for all versions of Yii.
 *
 * Version 2.0 provides also json
 */
class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['view', 'index'],
                'formats' => [
                    'text/html' => Response::FORMAT_HTML,
                    'application/xhtml+xml' => Response::FORMAT_HTML,
                    'application/json' => Response::FORMAT_JSON,
                    'json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }


    public function actionIndex($version)
    {
        $this->validateVersion($version);

        switch (Yii::$app->response->format) {
            case Response::FORMAT_HTML:
                return $this->actionView($version, 'index');
                break;
            case Response::FORMAT_JSON:
                $apiRenderer = new ApiRenderer([
                    'version' => $version,
                ]);

                $classes = Json::decode(file_get_contents(Yii::getAlias("@app/data/api-$version/json/typeNames.json")));
                foreach($classes as $i => $class) {
                    $classes[$i]['url'] = Yii::$app->request->hostInfo . $apiRenderer->generateApiUrl($class['name']);
                }
                return [
                    'classes' => $classes,
                    'count' => count($classes),
                ];
                break;
        }
        throw new UnsupportedMediaTypeHttpException;
    }

    public function actionView($version, $section)
    {
        $this->validateVersion($version);

        $title = '';
        $packages = [];
        if ($version[0] === '1') {
            $file = Yii::getAlias("@app/data/api-$version/api/$section.html");
            $packages = unserialize(file_get_contents(Yii::getAlias("@app/data/api-$version/api/packages.txt")));
            $view = 'view1x';
            $title = $section !== 'index' ? $section : '';
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

    protected function validateVersion($version)
    {
        $versions = Yii::$app->params['api.versions'];
        if (!in_array($version, $versions)) {
            // TODO make nicer error page (keep version and language selector)
            throw new NotFoundHttpException('The requested version was not found.');
        }
    }
}
