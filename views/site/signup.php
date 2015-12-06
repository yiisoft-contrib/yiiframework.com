<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\SignupForm */

$this->title = 'Create a new account';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup container">
    <div class="omb_login">
        <h3 class="omb_authTitle"><?= Html::encode($this->title) ?> or <?= Html::a('Login', Url::to(['site/login'])) ?></h3>
        <div class="row omb_row-sm-offset-3 omb_socialButtons">
            <div class="col-xs-4 col-sm-2">
                <!-- <a href="#" class="btn btn-lg btn-block omb_btn-facebook">
                    <i class="fa fa-facebook visible-xs"></i>
                    <span class="hidden-xs">Facebook</span>
                </a> -->
            </div>
            <div class="col-xs-4 col-sm-2">
                    <a href="/auth?authclient=github" class="btn btn-lg btn-block omb_btn-github">
                    <i class="fa fa-github visible-xs"></i>
                    <span class="hidden-xs">Github</span>
                </a>
            </div>
            <div class="col-xs-4 col-sm-2">
                <!-- <a href="#" class="btn btn-lg btn-block omb_btn-google">
                    <i class="fa fa-google-plus visible-xs"></i>
                    <span class="hidden-xs">Google+</span>
                </a> -->
            </div>
        </div>

        <div class="row omb_row-sm-offset-3 omb_loginOr">
            <div class="col-xs-12 col-sm-6">
                <hr class="omb_hrOr">
                <span class="omb_spanOr">or</span>
            </div>
        </div>

        <div class="row omb_row-sm-offset-3">
            <div class="col-xs-12 col-sm-6">
                <?php $form = ActiveForm::begin(['id' => 'form-signup', 'options' => ['class' => 'omb_loginForm', 'autocomplete' => 'off']]); ?>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <?= $form->field($model, 'username', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('username')]])->label('') ?>
                    </div>
                    <span class="help-block"></span>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                        <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('email')]])->label('') ?>
                    </div>
                    <span class="help-block"></span>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <?= $form->field($model, 'password', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('password')]])->passwordInput()->label('') ?>
                    </div>
                    <span class="help-block"><!-- Password error --></span>

                    <?= Html::submitButton('Sign Up', ['class' => 'btn btn-lg btn-block btn-primary', 'name' => 'signup-button']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
