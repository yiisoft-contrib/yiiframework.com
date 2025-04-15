<?php

return [

    // components

    'components.db' => [
        'class' => yii\db\Connection::class,
        'dsn' => 'mysql:host=localhost;dbname=yiiframework',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'on afterOpen' => function($event) {
            /** @var $db \yii\db\Connection */
            $db = $event->sender;
            $db->createCommand("SET time_zone = '+00:00';")->execute();
        },
    ],
    'components.forumAdapter' => [
        'class' => app\components\forum\DummyAdapter::class,
    ],
    'components.cache' => YII_ENV === 'test' ? ['class' => yii\caching\FileCache::class] : [
        'class' => yii\redis\Cache::class,
        'redis' => [
            'class' => yii\redis\Connection::class,
            'hostname' => YII_ENV === 'prod' ? 'localhost' : 'redis',
            'database' => 1,
        ],
    ],
    'components.mailer' => [
        'class' => yii\swiftmailer\Mailer::class,
        'viewPath' => '@app/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => true,
    ],
    'components.elasticsearch' => [
        'class' => yii\elasticsearch\Connection::class,
        'nodes' => [
            ['http_address' => '127.0.0.1:9200'],
            // configure more hosts if you have a cluster
        ],
    ],
    'components.queue' => [
        'class' => yii\queue\db\Queue::class,
        'db' => 'db', // DB connection component or its config
        'tableName' => '{{%queue}}', // Table name
        'channel' => 'default', // Queue channel key
        'mutex' => [
            'class' => yii\mutex\MysqlMutex::class, // Mutex that used to sync queries
            'db' => 'db',
        ],
    ],
    'components.fs' => [
        'class' => creocoder\flysystem\LocalFilesystem::class,
        'path' => '@app/data/files',
    ],
    'components.formatter' => [
        'class' => app\components\Formatter::class,
        'thousandSeparator' => '&thinsp;',
    ],
    'components.urlManager' => [
        'enablePrettyUrl' => true,
        'showScriptName' => false,
        'enableStrictParsing' => true,
        'rules' => require __DIR__ . '/urls.php',
        'normalizer' => [
            'class' => yii\web\UrlNormalizer::class,
            'action' => YII_DEBUG ? \yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY : \yii\web\UrlNormalizer::ACTION_REDIRECT_PERMANENT,
        ],
    ],
    'components.authManager' => [
        'class' => yii\rbac\DbManager::class,
    ],

    // api and guide

    // base url for generating api documentation
    'api.baseUrl' => '/doc/api',
    'guide.baseUrl' => '/doc/guide',
    'blogtut.baseUrl' => '/doc/blog',
    'baseUrl' => '',

    'guide.versions' => [
        '2.0' => [
            'ar' => 'العربية',  // Arabic
            'en' => 'English',
            'es' => 'Español',     // Spanish
            'fr' => 'Français',     // French
            'id' => 'Bahasa Indonesia', // Indonesian
            'ja' => '日本語',       // Japanese
            'pl' => 'Polski',       // Polish
            'pt-br' => 'Português brasileiro',  // Brazilian Portuguese
            'ru' => 'Русский',     // Russian
            'uk' => 'Українська', // Ukrainian
            'uz' => 'Oʻzbekcha', // Uzbek
            'zh-cn' => '简体中文',  // Simplified Chinese
            'vi' => 'Tiếng Việt', // Vietnamese
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
            'uk' => 'Украї́нська', // Ukrainian
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
        'id' => 'bahasai', // https://tex.stackexchange.com/questions/224231/what-is-difference-indonesian-babel-with-bahasa-babel
        'ja' => '', // custom code in GuideController
        'pl' => 'polish',
        'pt-br' => 'brazilian',
        'ru' => 'russian',
        'zh-cn' => '', // custom code in GuideController
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
            'uk' => 'Украї́нська', // Ukrainian
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

	'versions' => require __DIR__ . '/versions.php',

    'books2' => require __DIR__ . '/books2.php',
    'books1' => require __DIR__ . '/books1.php',
    'testimonials' => require __DIR__ . '/testimonials.php',
    'members' => require __DIR__ . '/members.php',
    'release-cycle' => require __DIR__ . '/release-cycle.php',
    'donation-services' => require __DIR__ . '/donation-services.php',

    'adminEmail' => 'hostmaster@yiiframework.com',
    'supportEmail' => 'team@yiiframework.com',
    'securityEmails' => [
        'sam@rmcreative.ru',
        'contact@cebe.cc',
        'd.naumenko.a@gmail.com',
    ],
    'notificationEmail' => ['admin@yiiframework.com' => 'Yii Framework'],
    'user.passwordResetTokenExpire' => 3600 * 3, // 3 hours
    'user.emailVerificationTokenExpire' => 3600 * 3, // 3 hours
    'user.rememberMeDuration' => 3600 * 24 * 30, // 30 days

    // cache
    'cache.extensions.search' => 300,
    'cache.extensions.get' => 300,

    'sitemap.indexMaxUrls' => 10000, //Count url in one sitemap index file

    // configure this in local config
    'siteAbsoluteUrl' => null,
    'recaptcha.key' => null,
    'recaptcha.secret' => null,
    'recaptcha.enabled' => false,

    // configuration for Discourse Forum SSO
    // https://meta.discourse.org/t/official-single-sign-on-for-discourse-sso/13045
    // configure Discourse to point SSO requests to https://www.yiiframework.com/auth/discourse-sso
    'discourse.sso_secret' => '',
    'discourse.sso_url' => 'https://forum.yiiframework.com',
    'slack.invite.link' => 'https://join.slack.com/t/yii/shared_invite/enQtMzQ4MDExMDcyNTk2LTc0NDQ2ZTZhNjkzZDgwYjE4YjZlNGQxZjFmZDBjZTU3NjViMDE4ZTMxNDRkZjVlNmM1ZTA1ODVmZGUwY2U3NDA',
];
