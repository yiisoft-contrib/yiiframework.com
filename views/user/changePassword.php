<?php
/**
 * @var View $this
 * @var ChangePasswordForm $changePasswordForm
 */

use app\models\ChangePasswordForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = "Change password";
?>
<div class="container style_external_links account-form-page">
    <div class="content account-form-page__content">

        <h1><?= Html::encode($this->title) ?></h1>
        <p class="account-form-page__intro">Choose a new password for your Yii account.</p>

        <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">

                    <?= $form->field($changePasswordForm, 'currentPassword')->passwordInput() ?>
                    <?= $form->field($changePasswordForm, 'password')->passwordInput([
                        'autocomplete' => 'off',
                    ])->hint('The minimum length is 6 characters.') ?>
                    <?= $form->field($changePasswordForm, 'passwordRepeat')->passwordInput([
                        'autocomplete' => 'off',
                    ]) ?>

                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
