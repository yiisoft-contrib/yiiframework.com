<?php

return [
    'bootstrap' => [
        'debug', 'gii',
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'elasticsearch' => [
                    'class' => 'yii\\elasticsearch\\DebugPanel',
                ],
            ],
        ],
        'gii' => [
            'class' => 'yii\\gii\\Module',
        ],
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
        ],
    ],
];
