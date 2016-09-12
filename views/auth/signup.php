<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\SignupForm */

$this->title = 'Sign Up';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container login-container">

    <div class="omb_login row">

      <div class="col-md-3 col-md-offset-3">

        <div class="omb_authTitle">
            <h3><?= Html::encode($this->title) ?></h3>
            <span>or</span>
             <?= Html::a('Login', Url::to(['auth/login']),['class'=>'create-account']) ?>
        </div>

            <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'omb_loginForm', 'autocomplete' => 'off']]); ?>
               
            <?= $form->field($model, 'username', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('username')]])->label(false) ?>

            <?= $form->field($model, 'email', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('email')]])->label(false) ?>

            <?= $form->field($model, 'password', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('password')]])->passwordInput()->label(false) ?>

            <?= Html::submitButton('Create New Account', ['class' => 'btn btn-lg btn-block']) ?>
            
            <?php ActiveForm::end(); ?>

      </div>
      <div class="col-md-3 col-md-offset-1 social-login">
            <span class="github-icon">
                <i class="fa fa fa-github-square"></i>
            </span>
            <h4>Did you sign up with your<br/>Github Account?</h4>
            <?= Html::a('Login with Github', '/auth?authclient=github',['class' => 'btn btn-lg']) ?>
      </div>

    </div>

</div>

