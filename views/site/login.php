<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container login-container">

    <div class="omb_login row">

      <div class="col-md-3 col-md-offset-3">

        <div class="omb_authTitle">
            <h3><?= Html::encode($this->title) ?></h3>
            <span>or</span>
             <?= Html::a('Create a new account', Url::to(['site/signup']),['class'=>'create-account']) ?>
        </div>

            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'omb_loginForm', 'autocomplete' => 'off']]); ?>
               
            <?= $form->field($model, 'username', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('username')]])->label(false) ?>
            <span class="help-block"></span>

            <?= $form->field($model, 'password', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('password')]])->passwordInput()->label(false) ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <?= Html::submitButton('Login', ['class' => 'btn btn-lg btn-block']) ?>
            
            <?php ActiveForm::end(); ?>

            <p class="forgotPwd">
                <?= Html::a('Forgot password?', Url::to(['site/request-password-reset'])) ?>
            </p>

      </div>
      <div class="col-md-3 col-md-offset-1 social-login">
            <span class="github-icon">
                <i class="fa fa fa-github-square"></i>
            </span>
            <h4>Did you sign up with your<br/>Github Account?</h4>
            <?= Html::a('Login with Github', ['site/auth', 'authclient' => 'github'], ['class' => 'btn btn-lg']) ?>
      </div>

    </div>

</div>
