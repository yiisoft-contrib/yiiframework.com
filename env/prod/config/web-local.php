<?php

return [
    'bootstrap' => [
        'rollbar',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
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
