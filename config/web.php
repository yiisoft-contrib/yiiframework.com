<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$config = [
    'id' => 'yiiframework.com',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'db' => $params['components.db'],
        'elasticsearch' => $params['components.elasticsearch'],
        'cache' => $params['components.cache'],
        'mailer' => $params['components.mailer'],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => false,
//                [
//                'yii\bootstrap\BootstrapAsset' => false,
//                'yii\bootstrap\BootstrapPluginAsset' => false,
//                'yii\web\YiiAsset' => false,
//                'yii\validators\ValidationAsset' => false,
//                'yii\web\JqueryAsset' => false,
//                'yii\bootstrap\BootstrapAsset2' => [
//                    'sourcePath' => null,
//                    'css' => [
//                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/readable/bootstrap.min.css',
////                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/cerulean/bootstrap.min.css',
////                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/cosmo/bootstrap.min.css'
////                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/flatly/bootstrap.min.css'
//                    ]
//                ]
//            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => require(__DIR__ . '/urls.php'),
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => $params['authclients'],
        ],
    ],
    'params' => $params,

    // URLs with trailing slashes should be redirected to URLs without trailig slashes
    'on beforeRequest' => function () {
        $pathInfo = Yii::$app->request->pathInfo;
        $query = Yii::$app->request->queryString;
        if (!empty($pathInfo) && substr($pathInfo, -1) === '/') {
            $url = '/' . substr($pathInfo, 0, -1);
            if ($query) {
                $url .= '?' . $query;
            }
            Yii::$app->response->redirect($url, 301);
        }
    },
];

return $config;
