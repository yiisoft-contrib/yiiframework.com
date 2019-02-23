<?php

/** @var $this \yii\web\View */
/** @var $model \app\models\Linkable */
/** @var $user \app\models\User */
/** @var $title string */
/** @var $css string optional css to be added to the header */
/** @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;

?>
<html>
<head>
    <title><?= Html::encode($title); ?></title>
    <style>
        <?= $css ?? '' ?>
    </style>
</head>
<body>
    <p>
        Dear <?= Html::encode($user->display_name); ?>,
    </p>

    <?= $content ?>

    <p>
        To stop receiving such notification in the future, visit
        <?= Html::a('the tutorial page', Url::to($model->getUrl(), true)); ?>
        and click on the star icon to stop following it.<br/>
        You may also manage your subscriptions at your
        <?= Html::a('account profile page', Url::to($user->getUrl('profile'), true)); ?>.
    </p>
    <p>
        <b>PLEASE DO NOT REPLY TO THIS EMAIL AS IT IS SENT FROM OUR AUTOMATED SYSTEM.</b><br/>
        <?= Html::a('Yii Framework', 'http://www.yiiframework.com'); ?>
    </p>
</body>
</html>
