<?php

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PartnersForm */

$this->title = 'Find a development partner';
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container partners-page">
    <header class="partners-hero">
        <div class="partners-hero__content">
            <h1>Build your Yii project with the right team</h1>
            <p class="partners-hero__lead">From new products to complex upgrades and long-term maintenance, we’ll help connect you with experienced Yii developers who fit the work.</p>
            <div class="partners-hero__actions">
                <a class="btn btn-primary" href="#project-request-title">Tell us about your project</a>
                <span>No obligation. Start with a project brief.</span>
            </div>
        </div>
    </header>

    <ul class="partners-benefits" aria-label="Why find a partner through Yii">
        <li><strong>Yii expertise</strong><span>Developers who understand the framework and its ecosystem.</span></li>
        <li><strong>Practical matching</strong><span>Your scope is considered before an introduction is made.</span></li>
        <li><strong>Flexible engagement</strong><span>Suitable for focused fixes, upgrades, or complete projects.</span></li>
    </ul>

    <section class="partners-request" aria-labelledby="project-request-title">
        <div class="partners-request__intro">
            <h2 id="project-request-title">Tell us what you need</h2>
            <p>
                Share the essentials below. A Yii team member may take on the work independently or introduce you
                to a trusted development partner with relevant experience.
            </p>
        </div>

        <?php if (Yii::$app->session->hasFlash('partnersFormSubmitted')): ?>
            <div class="alert alert-success partners-request__success">
                Thank you for contacting us. We will respond as soon as possible.
            </div>
        <?php else: ?>
            <div class="partners-request__layout">
                <div class="partners-request__form">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'partners-form']]) ?>

                    <?= $form->field($model, 'email')->textInput([
                        'autocomplete' => 'name',
                        'placeholder' => 'Your name',
                    ]) ?>

                    <?= $form->field($model, 'name')->textInput([
                        'autocomplete' => 'email',
                        'placeholder' => 'you@example.com',
                    ]) ?>

                    <?= $form->field($model, 'company')->textInput([
                        'autocomplete' => 'organization',
                        'placeholder' => 'Company or organization',
                    ]) ?>

                    <?= $form->field($model, 'body')->textarea([
                        'rows' => 8,
                        'placeholder' => 'Describe the project, its goals, scope, and technical requirements.',
                    ]) ?>

                    <?= $form->field($model, 'budget')->textInput([
                        'placeholder' => 'Estimated budget and currency',
                    ]) ?>

                    <?= $form->field($model, 'when')->textInput([
                        'placeholder' => 'Preferred start date and deadline',
                    ]) ?>

                    <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                        'template' => '<div class="partners-captcha">{image}{input}</div>',
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Enter the verification code',
                            'autocomplete' => 'off',
                        ],
                    ]) ?>

                    <div class="partners-form__submit">
                        <?= Html::submitButton('Find the right partner', [
                            'class' => 'btn btn-primary',
                            'name' => 'contact-button',
                        ]) ?>
                        <span>We’ll review your brief and follow up about the best next step.</span>
                    </div>

                    <?php ActiveForm::end() ?>
                </div>

                <aside class="partners-request__tips">
                    <div class="partners-next-steps">
                        <h3>What happens next</h3>
                        <ol>
                            <li><span>1</span><div><strong>We review your brief</strong><p>We look at the scope, Yii version, timing, and budget.</p></div></li>
                            <li><span>2</span><div><strong>We identify a fit</strong><p>A team member or trusted partner is selected for the work.</p></div></li>
                            <li><span>3</span><div><strong>You discuss the project</strong><p>You agree on the approach and terms directly with them.</p></div></li>
                        </ol>
                    </div>
                    <div class="partners-request__checklist">
                        <h3>A useful brief includes</h3>
                        <ul>
                            <li>Goals and required features</li>
                            <li>Current stack and Yii version</li>
                            <li>Budget, timing, and deadline</li>
                        </ul>
                        <p>Not sure yet? Enter “N/A” and we’ll start from there.</p>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </section>
</main>
