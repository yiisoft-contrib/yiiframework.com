<?php

$params = array_merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'yiiframework.com-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        'yii\apidoc\templates\' . $template',
        '@webroot' => '@app/web'
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            // use custom migration template
            'templateFile' => '@app/migrations/migration_template.php',
        ],
    ],
    'components' => [
        'db' => $params['components.db'],
        'elasticsearch' => $params['components.elasticsearch'],
        'cache' => $params['components.cache'],
        'mailer' => $params['components.mailer'],
        'urlManager' => array_merge(
            $params['components.urlManager'],
            [
                'baseUrl' => '',
                'hostInfo' => $params['siteAbsoluteUrl']
            ]
        ),
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
