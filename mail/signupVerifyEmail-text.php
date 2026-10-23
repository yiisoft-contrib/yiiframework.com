<?php
/** @var yii\web\View $this */
/** @var \app\models\User $user */

$verifyEmailLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/verify-email', 'token' => $user->email_verification_token]);
?>

Welcome to Yii framework community!

Follow the link below to confirm your email:

<?= $verifyEmailLink ?>
