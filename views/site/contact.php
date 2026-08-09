<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
?>
<main class="container contact-page">
    <div class="content contact-page__layout">
        <header class="contact-page__intro">
            <p>Contact</p>
            <h1>Let’s talk.</h1>
            <div>For business inquiries and questions that do not belong in the forum or issue tracker, send us a message.</div>
        </header>
        <section class="contact-page__form" aria-label="Contact form">
                <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>
                    <div class="alert alert-success">
                        Thank you for contacting us. We will respond to you as soon as possible.
                    </div>
                <?php else: ?>
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

                    <?= Html::submitButton('Send message', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                    <?php ActiveForm::end(); ?>
                <?php endif ?>
        </section>
    </div>
</main>
