<?php

return [
    'bootstrap' => [
        'debug',
        'gii',
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'elasticsearch' => yii\elasticsearch\DebugPanel::class,
                'queue' => yii\queue\debug\Panel::class,
            ],
        ],
        'gii' => [
            'class' => 'yii\\gii\\Module',
        ],
    ],
    'components' => [
        'session' => [
            'class' => yii\redis\Session::class,
            'redis' => [
                'class' => yii\redis\Connection::class,
                'hostname' => 'redis',
                'database' => 0,
            ],
            'cookieParams' => [
                'httponly' => true,
                'secure' => true,
            ],
        ],
        'request' => [
            'cookieValidationKey' => '',
        ],
    ],
];
