<?php

/** @var \yii\web\View $this */
/** @var \app\models\Linkable $model */
/** @var \app\models\User $user */
/** @var string $content */

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
Yii Framework (https://www.yiiframework.com)
