<?php
/* @var $this yii\web\View */
/* @var $user \app\models\User */

$verifyEmailLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/verify-email', 'token' => $user->email_verification_token]);
?>

Hello <?= $user->display_name ?>,

Follow the link below to confirm your email:

<?= $verifyEmailLink ?>
