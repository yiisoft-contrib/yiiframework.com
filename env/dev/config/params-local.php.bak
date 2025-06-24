<?php

return [
    'components.db' => [
        'class' => 'yii\db\Connection',
        'charset' => 'utf8',

        'dsn' => 'mysql:host=mariadb;dbname=yiiframeworkcom',
        'username' => 'yiiframeworkcom',
        'password' => 'yiiframeworkcom',
    ],

    'components.elasticsearch' => [
        'class' => yii\elasticsearch\Connection::class,
        'nodes' => [
            ['http_address' => 'elasticsearch:9200'],
        ],
    ],

    'authclients' => [
        'github' => [
            'class' => 'yii\authclient\clients\GitHub',
            'scope' => 'user:email',

            // register a new application on Github and enter client secrets here
            'clientId' => '',
            'clientSecret' => '',
        ],
    ],

    'siteAbsoluteUrl' => 'http://local.yiiframework.com',

    // https://apps.twitter.com/app/new
    // After creating an app you need to fill accessToken and accessTokenSecret:
    // Open App -> Keys and Access Tokens -> You Access Token -> Create my access token
    'twitter.consumerKey' => '',
    'twitter.consumerSecret' => '',
    'twitter.accessToken' => '',
    'twitter.accessTokenSecret' => '',
];
