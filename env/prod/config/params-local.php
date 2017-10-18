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

    'siteAbsoluteUrl' => 'https://yiiframework.com',

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
