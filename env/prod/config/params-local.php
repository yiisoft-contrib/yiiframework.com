<?php

$db = require __DIR__ . '/db.php';

return [
    'authclients' => [
        'github' => [
            'class' => 'yii\authclient\clients\GitHub',
            'clientId' => '',
            'clientSecret' => '',
            'scope' => 'user:email',
        ],
    ],

    'siteAbsoluteUrl' => 'https://www.yiiframework.com',
    'adminEmail' => 'admin@yiiframework.com',
    'supportEmail' => 'admin@yiiframework.com',

    'components.db' => [
        'class' => yii\db\Connection::class,
        'dsn' => $db['dsn'],
        'username' => $db['username'],
        'password' => $db['password'],
        'charset' => 'utf8',
        'on afterOpen' => function($event) {
            /** @var $db \yii\db\Connection */
            $db = $event->sender;
            $db->createCommand("SET time_zone = '+00:00';")->execute();
        },
    ],
    'components.forumAdapter' => [
        'class' => app\components\forum\DiscourseAdapter::class,
        'apiToken' => '',
        'apiUrl' => 'https://forum.yiiframework.com',
        'apiAdminUser' => 'system',
    ],
    'components.mailer' => [
        'class' => yii\swiftmailer\Mailer::class,
        'viewPath' => '@app/mail',
        'useFileTransport' => false,
    ],

     // @see https://apps.twitter.com/app/new
     // After creating an app you need to fill accessToken and accessTokenSecret:
     // Open App -> Keys and Access Tokens -> You Access Token -> Create my access token
    'twitter.consumerKey' => '',
    'twitter.consumerSecret' => '',
    'twitter.accessToken' => '',
    'twitter.accessTokenSecret' => '',

    // configure these to enable proxying external images through an nginx content filter
    'image-proxy' => 'https://user-content.yiiframework.com',
    'image-proxy-secret' => '',

    // configuration for Discourse Forum SSO
    // https://meta.discourse.org/t/official-single-sign-on-for-discourse-sso/13045
    // configure Discourse to point SSO requests to https://www.yiiframework.com/auth/discourse-sso
    'discourse.sso_secret' => '',
    'discourse.sso_url' => 'https://forum.yiiframework.com',
];
