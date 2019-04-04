<?php

use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\ContactForm */
?>
<div class="container">
        <div class="content">
            <div class="col-12 col-sm-6">
                <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
                    <div class="alert alert-success">
                        Thank you for contacting us. We will respond to you as soon as possible.
                    </div>
                <?php else: ?>
                    <p>
                        If you have business inquiries or other questions,<br>please fill out the following form to
                        contact
                        us.
                    </p>
                    <p>
                        Thank you.
                    </p>
                    <?php $form = ActiveForm::begin() ?>
                        <?= $form->field($model, 'name', [
                            'inputOptions' => ['placeholder' => $model->getAttributeLabel('name')]
                        ])->label(false) ?>

                        <?= $form->field($model, 'email', [
                            'inputOptions' => ['placeholder' => $model->getAttributeLabel('email')]
                        ])->label(false) ?>


                        <?= $form->field($model, 'subject', [
                            'inputOptions' => ['placeholder' => $model->getAttributeLabel('subject')]
                        ])->label(false) ?>

                        <?= $form->field($model, 'body', [
                            'inputOptions' => ['placeholder' => $model->getAttributeLabel('body')]
                        ])->label(false)->textarea(['rows' => 6]) ?>


                        <?= $form->field($model, 'verifyCode', [
                            'inputOptions' => ['placeholder' => 'Verification Code']
                        ])->label('Verification code: ')->widget(Captcha::class, [
                            'template' => '{image}{input}',
                        ]) ?>

                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    <?php ActiveForm::end(); ?>
                <?php endif ?>
            </div>
            <div class="clearfix"></div>
        </div>
</div>
