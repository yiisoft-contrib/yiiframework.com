<?php

use app\models\SignupForm;
use himiklab\yii2\recaptcha\ReCaptcha;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model SignupForm */

$this->title = 'Sign Up';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container login-container login-page signup-page">
    <main class="login-card">
        <header class="login-card__header">
            <h1>Join the Yii community</h1>
            <p>
                Already have an account?
                <?= Html::a('Login', ['auth/login']) ?>
            </p>
        </header>

        <div class="login-card__layout">
            <section class="login-card__account" aria-labelledby="create-account-title">
                <h2 id="create-account-title">Create your account</h2>

                <?php $form = ActiveForm::begin([
                    'id' => 'signup-form',
                    'options' => ['class' => 'login-form', 'autocomplete' => 'off'],
                ]); ?>

                <?= $form->field($model, 'username')->textInput([
                    'class' => 'form-control',
                    'autocomplete' => 'username',
                ]) ?>

                <?= $form->field($model, 'email')->textInput([
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                ]) ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'class' => 'form-control',
                    'autocomplete' => 'new-password',
                ]) ?>

                <?php if (Yii::$app->params['recaptcha.enabled']): ?>
                    <div class="signup-page__captcha">
                        <?= $form->field($model, 'reCaptcha')->widget(ReCaptcha::class)->label(false) ?>
                    </div>
                <?php endif; ?>

                <?= Html::submitButton('Create account', ['class' => 'btn btn-primary login-form__submit']) ?>

                <?php ActiveForm::end(); ?>
            </section>

            <section class="login-card__github" aria-labelledby="github-signup-title">
                <span class="login-card__github-icon" aria-hidden="true">
                    <i class="fa fa-github"></i>
                </span>
                <h2 id="github-signup-title">Continue with GitHub</h2>
                <p>Create your Yii community profile using your GitHub account.</p>
                <?= Html::a(
                    '<i class="fa fa-github" aria-hidden="true"></i><span>Sign up with GitHub</span>',
                    ['auth/auth', 'authclient' => 'github'],
                    ['class' => 'btn login-card__github-button']
                ) ?>
                <p class="login-card__github-note">To connect GitHub to an existing account, log in with your username first.</p>
            </section>
        </div>
    </main>
</div>
