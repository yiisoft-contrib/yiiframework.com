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
        'request' => [
            'cookieValidationKey' => '',
        ],
    ],
];
