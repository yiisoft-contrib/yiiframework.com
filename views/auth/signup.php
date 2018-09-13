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

      <div class="col-md-4 col-md-offset-3">

        <div class="omb_authTitle">
            <h3><?= Html::encode($this->title) ?></h3>
            <span>or</span>
             <?= Html::a('Login', Url::to(['auth/login']),['class'=>'create-account']) ?>
        </div>

        <div class="row">
          <div class="col-md-9">
            <?php $form = ActiveForm::begin(['id' => 'signup-form', 'options' => ['class' => 'omb_loginForm', 'autocomplete' => 'off']]); ?>
               
            <?= $form->field($model, 'username', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('username')]])->label(false) ?>

            <?= $form->field($model, 'email', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('email')]])->label(false) ?>

            <?= $form->field($model, 'password', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('password')]])->passwordInput()->label(false) ?>

            <?php if (Yii::$app->params['recaptcha.enabled']): ?>
            <?= $form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha::class)->label(false) ?>
            <?php endif ?>

            <?= Html::submitButton('Create New Account', ['class' => 'btn btn-lg btn-block']) ?>
            
            <?php ActiveForm::end(); ?>
          </div>
        </div>

      </div>
      <?= $this->render('partials/_githubLogin.php') ?>

    </div>

</div>

