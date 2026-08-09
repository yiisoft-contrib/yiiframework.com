<?php
/**
 * @var View $this
 * @var ChangeEmailForm $changeEmailForm
 */

use app\models\ChangeEmailForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Change email';
?>
<div class="container style_external_links account-form-page">
    <div class="content account-form-page__content">

        <h1><?= Html::encode($this->title) ?></h1>
        <p class="account-form-page__intro">Confirm your password, then enter the new email address for your account.</p>

        <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">

                    <?= $form->field($changeEmailForm, 'currentPassword')->passwordInput() ?>
                    <?= $form->field($changeEmailForm, 'email')->input('email') ?>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
