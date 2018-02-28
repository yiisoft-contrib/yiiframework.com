<?php
return [
    // if you develop yiiframework.com in a subdirectory under localhost
    // and not on a separate host name, enter the baseUrl here
//    'api.baseUrl' => '/doc/api',
//    'guide.baseUrl' => '/doc/guide',
//    'blogtut.baseUrl' => '/doc/blog',

    'components.db' => [
        'class' => 'yii\db\Connection',
        'charset' => 'utf8',

        // adjust DB credentials to your needs
        'dsn' => 'mysql:host=localhost;dbname=yiiframeworkcom',
        'username' => 'yiiframeworkcom',
        'password' => 'yiiframeworkcom',
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

    /**
     * @see https://apps.twitter.com/app/new
     *
     * After creating an app you need to fill accessToken and accessTokenSecret:
     *
     * Open App -> Keys and Access Tokens -> You Access Token -> Create my access token
     */
    'twitter.consumerKey' => '',
    'twitter.consumerSecret' => '',
    'twitter.accessToken' => '',
    'twitter.accessTokenSecret' => ''
];
