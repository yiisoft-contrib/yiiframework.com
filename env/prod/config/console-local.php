<?php

return [
    'bootstrap' => [
        'rollbar',
    ],
    'components' => [
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
