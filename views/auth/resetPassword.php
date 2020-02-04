<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \app\models\ResetPasswordForm */

$this->title = 'Reset password';
?>
<div class="container login-container">

    <div class="omb_login row">

      <div class="col-md-4 col-md-offset-3">

        <div class="omb_authTitle">
            <h3><?= Html::encode($this->title) ?></h3>
            <span>Please choose your new password.</span>
        </div>

        <div class="row">
          <div class="col-md-9">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form', 'options' => ['class' => 'omb_loginForm', 'autocomplete' => 'off']]); ?>

            <?= $form->field($model, 'password', ['inputOptions' => ['class'=>'login-control','placeholder' => $model->getAttributeLabel('password')]])->passwordInput()->label(false) ?>
            <span class="help-block"></span>

            <?= Html::submitButton('Change Password', ['class' => 'btn btn-lg btn-block']) ?>

            <?php ActiveForm::end(); ?>
          </div>
        </div>

      </div>
      <?= $this->render('partials/_githubLogin.php') ?>

    </div>

</div>
