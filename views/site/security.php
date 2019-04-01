<?php

use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecurityForm */

$this->title = 'Report a Security Issue';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container site-header">
    <div class="row">
        <div class="col-md-6">
            <h1 class="security">Report a<br>Security Issue</h1>
            <h2>Let's make Yii better</h2>
        </div>
        <div class="col-md-6">
            <img class="background" src="<?= Yii::getAlias('@web/image/issues/issues_secret.svg')?>" alt="">
        </div>
    </div>
</div>

<div class="container report">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (Yii::$app->session->hasFlash('securityFormSubmitted')): ?>
                    <div class="alert alert-success">
                        Thank you for contacting us. We will respond to you as soon as possible.
                    </div>
                <?php else: ?>
                    <p>Please use the security issue form to report to us any security issue
                        you find in Yii. DO NOT use the issue tracker or discuss it in the public forum as it will cause more damage
                        than help.</p>
                <?php endif ?>

                <div class="heading-separator">
                    <h2><span>Security Issue Form</span></h2>
                </div>
            </div>

            <div class="col-md-8">
                <?php $form = ActiveForm::begin() ?>
                <div class="row">
                    <div class="col-md-6">
						<?= $form->field($model, 'name', [
							'inputOptions' => [
								'placeholder' => $model->getAttributeLabel('name'),
								'aria-label' => $model->getAttributeLabel('name'),
							]
						])->label(false) ?>
                    </div>
                    <div class="col-md-6">
						<?= $form->field($model, 'email', [
							'inputOptions' => [
								'placeholder' => $model->getAttributeLabel('email'),
								'aria-label' => $model->getAttributeLabel('email'),
							]
						])->label(false) ?>
                    </div>
                </div>


                <?= $form->field($model, 'body', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('body'),
                        'aria-label' => $model->getAttributeLabel('body'),
                    ]
                ])->label(false)->textarea(['rows' => 6]) ?>


                <?= $form->field($model, 'verifyCode', [
                    'inputOptions' => ['placeholder' => 'Verification Code']
                ])->label('Verification code: ')->widget(Captcha::class, [
                    'template' => '<div class="row"><div class="col-md-2">{image}</div><div class="col-md-4">{input}</div></div>',
                ]) ?>

                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                <?php ActiveForm::end() ?>
            </div>
            <div class="col-md-4" style="background: #f3ffbd; color: #464646; padding: 1rem;">
                <p class="small">Once we receive your issue report, we will treat it as our highest priority. We will generally take the
                    following steps in responding to security issues.</p>

                <ol class="issue-process">
                    <li>Confirm the issue. We may contact with you for further discussion. We will send you an acknowledgement
                        after the issue is confirmed.
                    </li>
                    <li>Work on a solution.</li>
                    <li>Release a patch to all maintained versions.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
