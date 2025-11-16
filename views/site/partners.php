<?php
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PartnersForm */

$this->title = 'Find a development partner';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container site-header">
    <div class="row">
        <div class="col-md-6">
            <h1 class="">Need the project done?</h1>
            <h2>We'll help you find the right people!</h2>
        </div>
        <div class="col-md-6">
            <img class="background" src="<?= Yii::getAlias('@web/image/partners/partners.svg')?>" alt="" style="margin-top: 10rem">
        </div>
    </div>
</div>

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (Yii::$app->session->hasFlash('partnersFormSubmitted')): ?>
                    <div class="alert alert-success">
                        Thank you for contacting us. We will respond to you as soon as possible.
                    </div>
                <?php else: ?>
                    <p>Please use the project request form to send us details about your project.</p>

                    <p>Note that Yii is a non-commercial collective so either team members will take the project
                       as individuals or forward it to one of the trusted partners.</p>
                <?php endif ?>

                <div class="heading-separator">
                    <h2><span>Project Request Form</span></h2>
                </div>
            </div>

            <div class="col-md-9">
                <?php $form = ActiveForm::begin() ?>
                <?= $form->field($model, 'name', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('name'),
                        'aria-label' => $model->getAttributeLabel('name'),
                    ]
                ])->label(false) ?>

                <?= $form->field($model, 'email', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('email'),
                        'aria-label' => $model->getAttributeLabel('email'),
                    ]
                ])->label(false) ?>

                <?= $form->field($model, 'company', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('company'),
                        'aria-label' => $model->getAttributeLabel('company'),
                    ]
                ])->label(false) ?>

                <?= $form->field($model, 'body', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('body'),
                        'aria-label' => $model->getAttributeLabel('body'),
                    ]
                ])->label(false)->textarea(['rows' => 6]) ?>

                <?= $form->field($model, 'budget', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('budget'),
                        'aria-label' => $model->getAttributeLabel('budget'),
                    ]
                ])->label(false) ?>

                <?= $form->field($model, 'when', [
                    'inputOptions' => [
                        'placeholder' => $model->getAttributeLabel('when'),
                        'aria-label' => $model->getAttributeLabel('when'),
                    ]
                ])->label(false) ?>


                <?= $form->field($model, 'verifyCode', [
                    'inputOptions' => ['placeholder' => 'Verification Code']
                ])->label('Verification code: ')->widget(Captcha::class, [
                    'template' => '{image}{input}',
                ]) ?>

                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                <?php ActiveForm::end() ?>
            </div>
            <div class="col-md-3">
                <p class="small">Specifying project details, estimated budget along with currency, time to start and deadlines will help us
                    find the right people.</p>

                <p class="small">If something is not clear at this stage, feel free to specify it as N/A.</p>
            </div>
        </div>
    </div>
</div>
