<?php
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../config/web.php',
    [
        'components' => [
            'db' => require __DIR__ . '/test_db.php',
            'assetManager' => [
                'basePath' => __DIR__ . '/../web/assets',
            ],
            'request' => [
                'cookieValidationKey' => 'test',
                'enableCsrfValidation' => false,
            ],
        ],
    ]
);
return $config;
