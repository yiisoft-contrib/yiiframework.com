<?php

return [
    'bootstrap' => [
        'rollbar',
    ],
    'components' => [
        'cache' => [
            'class' => yii\redis\Cache::class,
            'redis' => [
                'class' => yii\redis\Connection::class,
                'database' => 1,
            ],
        ],
        'errorHandler' => [
            'class' => 'baibaratsky\yii\rollbar\console\ErrorHandler',
        ],
        'rollbar' =>  [
            'class' => 'baibaratsky\yii\rollbar\Rollbar',
            'accessToken' => '',
            'environment' => YII_ENV,
        ],
    ],
];
