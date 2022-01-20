<?php
define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

$vendorDir = getenv('VENDOR_DIR') ?: __DIR__ . '/../vendor';

require_once($vendorDir . '/yiisoft/yii2/Yii.php');
require $vendorDir . '/autoload.php';
