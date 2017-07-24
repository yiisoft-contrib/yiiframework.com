<?php

/** @var $this \yii\web\View */
/** @var $model \app\models\Linkable */
/** @var $user \app\models\User */
/** @var $content string */

use yii\helpers\Url;

?>
Dear <?= $user->display_name; ?>,

<?= $content ?>

To stop receiving such notification in the future, visit the page
<?= Url::to($model->getUrl(), true); ?>

and click on the star icon to stop following it.

You may also manage your subscriptions at your account profile page
<?= Url::to($user->getUrl('profile'), true); ?>


PLEASE DO NOT REPLY TO THIS EMAIL AS IT IS SENT FROM OUR AUTOMATED SYSTEM.
Yii Framework (http://www.yiiframework.com)
