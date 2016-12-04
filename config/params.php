<?php

return [

    // components

    'components.db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=yiiframework',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'components.cache' => [
        'class' => YII_DEBUG ? 'yii\caching\DummyCache' : 'yii\caching\FileCache',
    ],
    'components.mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@app/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => true,
    ],
    'components.elasticsearch' => [
        'class' => 'yii\elasticsearch\Connection',
        'nodes' => [
            ['http_address' => '127.0.0.1:9200'],
            // configure more hosts if you have a cluster
        ],
    ],
    'components.urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'enableStrictParsing' => true,
        'rules' => require(__DIR__ . '/urls.php')
    ],

    // api and guide

    // base url for generating api documentation
    'api.baseUrl' => '/doc/api',
    'guide.baseUrl' => '/doc/guide',
    'blogtut.baseUrl' => '/doc/blog',

    'guide.versions' => [
        '2.0' => [
            'en' => 'English',
            'es' => 'Español',     // Spanish
            'fr' => 'Français',     // French
            'ja' => '日本語',       // Japanese
            'pl' => 'Polski',       // Polish
            'pt-br' => 'Português brasileiro',  // Brazilian Portuguese
            'ru' => 'Русский',     // Russian
            'zh-cn' => '简体中文',  // Simplified Chinese
        ],
        '1.1' => [
            'de' => 'Deutsch',      // German
            'en' => 'English',      // English
            'es' => 'Español',      // Spanish
            'fr' => 'Français',     // French
            'he' => 'עִבְרִית',     // Hebrew
            'id' => 'Bahasa Indonesia', // Indonesian
            'it' => 'Italiano',     // Italian
            'ja' => '日本語',       // Japanese
            'pl' => 'Polski',       // Polish
            'pt' => 'Português',    // Portuguese
            'pt-br' => 'Português brasileiro',  // Brazilian Portuguese
            'ro' => 'România',      // Romanian
            'ru' => 'Русский',      // Russian
            'sv' => 'Svenska',      // Swedish
            'uk' => 'украї́нська', // Ukrainian
            'zh-cn' => '简体中文',  // Simplified Chinese
        ],
        '1.0' => [
            'de' => 'Deutsch',      // German
            'en' => 'English',      // English
            'es' => 'Español',      // Spanish
            'fr' => 'Français',     // French
            'he' => 'עִבְרִית',     // Hebrew
            'id' => 'Bahasa Indonesia', // Indonesian
            'ja' => '日本語',       // Japanese
            'pl' => 'Polski',       // Polish
            'pt' => 'Português',    // Portuguese
            'ro' => 'România',      // Romanian
            'ru' => 'Русский',      // Russian
            'sv' => 'Svenska',      // Swedish
            'zh-cn' => '简体中文',  // Simplified Chinese
        ],
    ],

    // guide languages for PDF creation
    'guide-pdf.languages' => [
        // language => latex babel language
        // https://en.wikibooks.org/wiki/LaTeX/Internationalization
        'de' => 'ngerman',
        'en' => 'british',
        'es' => 'spanish',
        'fr' => 'frenchb',
//        'ja' => '', // custom code in GuideController
        'pl' => 'polish',
        'pt-br' => 'brazilian',
        'ru' => 'russian',
//        'zh-cn' => '', // custom code in GuideController
    ],

    'blogtut.versions' => [
        '1.1' => [
            'en' => 'English',      // English
            'es' => 'Español',      // Spanish
            'id' => 'Bahasa Indonesia', // Indonesian
            'ja' => '日本語',       // Japanese
            'pl' => 'Polski',       // Polish
            'pt' => 'Português',    // Portuguese
            'pt-br' => 'Português brasileiro',  // Brazilian Portuguese
            'ru' => 'Русский',      // Russian
            'uk' => 'украї́нська', // Ukrainian
            'zh-cn' => '简体中文',  // Simplified Chinese
        ],
        '1.0' => [
            'en' => 'English',      // English
            'ja' => '日本語',       // Japanese
            'pl' => 'Polski',       // Polish
            'ru' => 'Русский',      // Russian
        ],
    ],

    'authclients' => [
        // these should be configured in local config
    ],

	'versions' => require 'versions.php',

    'books2' => require 'books2.php',
    'books1' => require 'books1.php',
    'testimonials' => require 'testimonials.php',
    'members' => require 'members.php',

    'adminEmail' => 'yii@cebe.cc',
    'supportEmail' => 'yii@cebe.cc',
    'user.passwordResetTokenExpire' => 3600 * 3, // 3 hours

    // cache
    'cache.extensions.search' => 300,
    'cache.extensions.get' => 300,

    'sitemap.indexMaxUrls' => 10000, //Count url in one sitemap index file
];
