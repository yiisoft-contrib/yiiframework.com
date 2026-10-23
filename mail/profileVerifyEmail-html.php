<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var \app\models\User $user */

$verifyEmailLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/verify-email', 'token' => $user->email_verification_token]);
?>

<p>Hello <?=Html::encode($user->display_name) ?>,</p>

<p>Follow the link below to confirm your email:</p>

<p><?= Html::a(Html::encode($verifyEmailLink), $verifyEmailLink) ?></p>
