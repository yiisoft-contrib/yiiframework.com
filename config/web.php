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
        'cache' => $params['components.cache'],
        'mailer' => $params['components.mailer'],
        'user' => [
            'identityClass' => 'app\models\User',
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
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => false,
                'yii\bootstrap\BootstrapPluginAsset' => false,
                'yii\web\YiiAsset' => false,
                'yii\validators\ValidationAsset' => false,
                'yii\web\JqueryAsset' => false,
                'yii\bootstrap\BootstrapAsset2' => [
                    'sourcePath' => null,
                    'css' => [
                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/readable/bootstrap.min.css',
//                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/cerulean/bootstrap.min.css',
//                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/cosmo/bootstrap.min.css'
//                        '//maxcdn.bootstrapcdn.com/bootswatch/3.3.0/flatly/bootstrap.min.css'
                    ]
                ]
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => require(__DIR__ . '/urls.php'),
        ],
    ],
    'params' => $params,
];

return $config;
