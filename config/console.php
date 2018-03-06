<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'yiiframework.com-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
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
            'migrationPath' => '@app/migrations',
            'migrationNamespaces' => [
                'yii\queue\db\migrations',
            ],
        ],
    ],
    'language' => 'en',
    'timeZone' => 'UTC',
    'components' => [
        'cache' => $params['components.cache'],
        'db' => $params['components.db'],
        'elasticsearch' => $params['components.elasticsearch'],
        'fs' => $params['components.fs'],
        'mailer' => $params['components.mailer'],
        'queue' => $params['components.queue'],
        'formatter' => $params['components.formatter'],
        'urlManager' => array_merge(
            $params['components.urlManager'],
            [
                'baseUrl' => '',
                'hostInfo' => $params['siteAbsoluteUrl']
            ]
        ),
        'authManager' => $params['components.authManager'],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@runtime/logs/console_errors.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@runtime/logs/console_warnings.log'
                ],
            ],
        ],
    ],
    'params' => $params,
];
