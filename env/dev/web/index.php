<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$vendorDir = getenv('VENDOR_DIR') ?: __DIR__ . '/../vendor';

require($vendorDir . '/autoload.php');
require($vendorDir . '/yiisoft/yii2/Yii.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../config/web.php'),
    require(__DIR__ . '/../config/web-local.php')
);

(new yii\web\Application($config))->run();
