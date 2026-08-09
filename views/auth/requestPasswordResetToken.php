<?php

use app\models\PasswordResetRequestForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model PasswordResetRequestForm */

$this->title = 'Request password reset';
?>
<main class="container login-container login-page auth-task-page">
    <div class="login-card auth-task-card">
        <header class="login-card__header">
            <p class="login-card__eyebrow">Account recovery</p>
            <h1><?= Html::encode($this->title) ?></h1>
            <p>Enter your email and we’ll send you a password reset link.</p>
        </header>
        <div class="login-card__password">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form', 'options' => ['class' => 'login-form', 'autocomplete' => 'off']]); ?>
            <?= $form->field($model, 'email')->textInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('email')])->label(false) ?>
            <?= Html::submitButton('Send reset link', ['class' => 'btn btn-primary login-form__submit']) ?>
            <?php ActiveForm::end(); ?>
        </div>
        <footer class="auth-task-card__footer">
            <?= Yii::$app->user->isGuest ? Html::a('Back to login', ['auth/login']) : Html::a('Back to profile', ['user/profile']) ?>
        </footer>
    </div>
</main>
