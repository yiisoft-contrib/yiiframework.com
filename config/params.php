<?php

return [
    'components.db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=yiiframework',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ],
    'components.cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'components.mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@app/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => true,
    ],
    'api.versions' => [
        '2.0',
        '1.1',
        '1.0',
    ],
    // base url for generating api documentation
    'api.baseUrl' => '/doc/api',
    'guide.versions' => [
        '2.0' => [
            'en' => 'English',
            'es' => 'Español',     // Spanish
            'ja' => '日本語',       // Japanese
            'ru' => 'Русский',     // Russian
            'zh-CN' => '简体中文',  // Simplified Chinese
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
            'pt_br' => 'Português brasileiro',  // Brazilian Portuguese
            'ro' => 'România',      // Romanian
            'ru' => 'Русский',      // Russian
            'sv' => 'Svenska',      // Swedish
            'uk' => 'украї́нська', // Ukrainian
            'zh_cn' => '简体中文',  // Simplified Chinese
        ],
    ],
    // base url for generating api documentation
    'guide.baseUrl' => '/doc/guide',
    'adminEmail' => 'admin@example.com',
];
