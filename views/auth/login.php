<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container login-container login-page">
    <main class="login-card">
        <header class="login-card__header">
            <h1>Welcome back</h1>
            <p>
                New to Yii?
                <?= Html::a('Create an account', ['auth/signup']) ?>
            </p>
        </header>

        <div class="login-card__layout">
            <section class="login-card__password" aria-labelledby="password-login-title">
                <h2 id="password-login-title">Login with your account</h2>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'login-form', 'autocomplete' => 'off'],
                ]); ?>

                <?= $form->field($model, 'username')->textInput([
                    'class' => 'form-control',
                    'autocomplete' => 'username',
                ]) ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                ]) ?>

                <div class="login-form__options">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <?= Html::a('Forgot password?', ['auth/request-password-reset']) ?>
                </div>

                <?= Html::submitButton('Login', ['class' => 'btn btn-primary login-form__submit']) ?>

                <?php ActiveForm::end(); ?>
            </section>

            <section class="login-card__github" aria-labelledby="github-login-title">
                <span class="login-card__github-icon" aria-hidden="true">
                    <i class="fa fa-github"></i>
                </span>
                <h2 id="github-login-title">Continue with GitHub</h2>
                <p>Use the GitHub account connected to your Yii community profile.</p>
                <?= Html::a(
                    '<i class="fa fa-github" aria-hidden="true"></i><span>Login with GitHub</span>',
                    ['auth/auth', 'authclient' => 'github'],
                    ['class' => 'btn login-card__github-button']
                ) ?>
                <p class="login-card__github-note">To connect GitHub to an existing account, log in with your username first.</p>
            </section>
        </div>
    </main>
</div>
