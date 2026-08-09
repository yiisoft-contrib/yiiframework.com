<?php

use app\models\ResetPasswordForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model ResetPasswordForm */

$this->title = 'Reset password';
?>
<main class="container login-container login-page auth-task-page">
    <div class="login-card auth-task-card">
        <header class="login-card__header">
            <p class="login-card__eyebrow">Account recovery</p>
            <h1><?= Html::encode($this->title) ?></h1>
            <p>Choose a secure new password for your Yii account.</p>
        </header>
        <div class="login-card__password">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form', 'options' => ['class' => 'login-form', 'autocomplete' => 'off']]); ?>
            <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control', 'placeholder' => $model->getAttributeLabel('password')])->label(false) ?>
            <?= Html::submitButton('Change password', ['class' => 'btn btn-primary login-form__submit']) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</main>
