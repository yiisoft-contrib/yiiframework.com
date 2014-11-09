<?php

return [
    'bootstrap' => [
        'debug',
    ],
    'modules' => [
        'debug' => 'yii\debug\Module',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
        ],
    ],
];
