<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Contact';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="omb_login">
        <h1 class="omb_authTitle"><?= Html::encode($this->title) ?></h1>
        <div class="row omb_row-sm-offset-3">
            <div class="col-xs-12 col-sm-6">
                <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
                    <div class="alert alert-success">
                        Thank you for contacting us. We will respond to you as soon as possible.
                    </div>
                <?php else: ?>
                    <p class="text-center">
                        If you have business inquiries or other questions,<br>please fill out the following form to contact us.
                    </p>
                    <p class="text-center">
                        Thank you.
                    </p>
                    <?php $form = ActiveForm::begin(['id' => 'login-form', 'options' => ['class' => 'omb_loginForm', 'autocomplete' => 'off']]); ?>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <?= $form->field($model, 'name', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('name')]])->label('') ?>
                        </div>
                        <span class="help-block"></span>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <?= $form->field($model, 'email', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('email')]])->label('') ?>
                        </div>
                        <span class="help-block"></span>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                            <?= $form->field($model, 'subject', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('subject')]])->label('') ?>
                        </div>
                        <span class="help-block"></span>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                            <?= $form->field($model, 'body', ['inputOptions' => ['placeholder' => $model->getAttributeLabel('body')]])->label('')->textArea(['rows' => 6]) ?>
                        </div>
                        <span class="help-block"></span>

                        <?= $form->field($model, 'verifyCode',['inputOptions' => ['placeholder' => 'Verification Code']])->label('Verification code: ')->widget(Captcha::className(), [
                            'template' => '{image}{input}',
                        ]) ?>

                        <?= Html::submitButton('Submit', ['class' => 'btn btn-lg btn-block btn-primary', 'name' => 'contact-button']) ?>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="row omb_row-sm-offset-3">
            <div class="col-xs-12 col-sm-3">
                &nbsp;
            </div>
            <div class="col-xs-12 col-sm-3">
                &nbsp;
            </div>
        </div>
    </div>
</div>
