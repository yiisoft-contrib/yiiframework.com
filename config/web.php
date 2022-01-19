<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config = [
    'id' => 'yiiframework.com',
    'name' => 'Yii Framework',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        app\components\BootstrapEvents::class,
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'language' => 'en',
    'timeZone' => 'UTC',
    'components' => [
        'session' => YII_ENV === 'test' ? [\yii\web\DbSession::class] : [
            'class' => yii\redis\Session::class,
            'redis' => [
                'class' => yii\redis\Connection::class,
                'hostname' => YII_ENV === 'prod' ? 'localhost' : 'redis',
                'database' => 0,
            ],
            'cookieParams' => [
                'httponly' => true,
                'secure' => true,
            ],
        ],
        'cache' => $params['components.cache'],
        'db' => $params['components.db'],
        'elasticsearch' => $params['components.elasticsearch'],
        'forumAdapter' => $params['components.forumAdapter'],
        'fs' => $params['components.fs'],
        'mailer' => $params['components.mailer'],
        'queue' => $params['components.queue'],
        'user' => [
            'as webuser' => app\components\WebUserBehavior::class,
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['auth/login'],
        ],
        'formatter' => $params['components.formatter'],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/web_errors.log',
                    'except' => [
                        'yii\web\HttpException:*',
                    ],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => [
                        'yii\web\HttpException:*',
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logFile' => '@runtime/logs/web_http_errors.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@runtime/logs/web_warnings.log'
                ],
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => false,
                'yii\validators\ValidationAsset' => false,
                'yii\web\YiiAsset' => false,
                'yii\widgets\ActiveFormAsset' => false,
                'yii\bootstrap\BootstrapPluginAsset' => false,
                'yii\web\JqueryAsset' => false,
                'dosamigos\selectize\SelectizeAsset' => [
                    'depends' => [
                        \app\assets\AppAsset::class,
                    ],
                ],
                'yii\jui\JuiAsset' => [
                    'depends' => [
                        \app\assets\AppAsset::class,
                    ],
                ],
                'yii\grid\GridViewAsset' => [
                    'depends' => [
                        \app\assets\AppAsset::class,
                    ],
                ],
                //'yii\authclient\widgets\AuthChoiceAsset' => false, //authchoice.js
                //'yii\authclient\widgets\AuthChoiceStyleAsset' => false, //authchoice.css
            ],
            'linkAssets' => true,
            'appendTimestamp' => true,
        ],
        'urlManager' => $params['components.urlManager'],
        'authManager' => $params['components.authManager'],
        'authClientCollection' => [
            'class' => \yii\authclient\Collection::class,
            'clients' => $params['authclients'],
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => \himiklab\yii2\recaptcha\ReCaptcha::class,
            'siteKey' => $params['recaptcha.key'],
            'secret' => $params['recaptcha.secret'],
        ],
    ],
    'params' => $params,
];

return $config;
