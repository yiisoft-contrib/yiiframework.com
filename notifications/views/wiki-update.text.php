<?php

/* @var $this \yii\web\View */
/* @var $wiki \app\models\Wiki the extension model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */
/* @var $changes \app\models\WikiRevision string, the changes */

use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.text.php', [
    'model' => $wiki,
    'user' => $user,
]); ?>
The following tutorial that you are following was recently updated.

TITLE:   <?= $wiki->title; ?>

URL:     <?= Url::to($wiki->getUrl(), true); ?>

UPDATED: <?= Yii::$app->formatter->asDatetime($changes->updated_at); ?>

SUMMARY: <?= $changes->memo; ?>

CHANGES: <?= Url::to($changes->getUrl(), true); ?>

<?php $this->endContent(); ?>
