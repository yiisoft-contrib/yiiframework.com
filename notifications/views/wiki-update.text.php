<?php
/**
 * @var $wiki \app\models\Wiki the extension model object that has been changed
 * @var $user \app\models\User the user object to whom the email is sent
 * @var $changes \app\models\WikiRevision string, the changes
 */

use yii\helpers\Url;

?>
Dear <?= $user->display_name; ?>,

The following tutorial that you are following was recently updated.

TITLE: <?= $wiki->title; ?> (<?= Url::to($wiki->getUrl(), true); ?>)
UPDATED: <?= Yii::$app->formatter->asDatetime($changes->updated_at); ?>

SUMMAY: <?= $changes->memo; ?>

CHANGES: <?= Url::to($changes->getUrl(), true); ?>

To stop receiving such notification in the future, visit the page
<?= Url::to($wiki->getUrl(), true); ?>

and click on the star icon to stop following it.
You may also manage your subscriptions at your account profile page
<?= Url::to($user->getUrl('profile'), true); ?>

PLEASE DO NOT REPLY TO THIS EMAIL AS IT IS SENT FROM OUR AUTOMATED SYSTEM.
Yii Framework (http://www.yiiframework.com)
