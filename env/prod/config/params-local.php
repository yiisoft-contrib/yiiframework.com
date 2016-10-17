<?php
return [
    'authclients' => [
        'github' => [
            'class' => 'yii\authclient\clients\GitHub',
            'clientId' => '',
            'clientSecret' => '',
            'scope' => 'user:email',
        ],
    ],

    'siteAbsoluteUrl' => 'https://yiiframework.com'
];
