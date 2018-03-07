<?php

return [
    'bootstrap' => [
        'rollbar',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
        ],
        'session' => [
            'class' => yii\redis\Session::class,
            'redis' => [
                'class' => yii\redis\Connection::class,
                'database' => 0,
            ],
        ],
        'cache' => [
            'class' => yii\redis\Cache::class,
            'redis' => [
                'class' => yii\redis\Connection::class,
                'database' => 1,
            ],
        ],
        'errorHandler' => [
            'class' => 'baibaratsky\yii\rollbar\web\ErrorHandler',
            'payloadDataCallback' => function (\baibaratsky\yii\rollbar\web\ErrorHandler $errorHandler) {
                return [
                    'exceptionCode' => $errorHandler->exception->getCode(),
                    'rawRequestBody' => Yii::$app->request->getRawBody(),
                ];
            },
            'errorAction' => 'site/error',
        ],
        'rollbar' =>  [
            'class' => 'baibaratsky\yii\rollbar\Rollbar',
            'accessToken' => '',
            'environment' => YII_ENV,
        ],
    ],
];
