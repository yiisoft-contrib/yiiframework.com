<?php
/**
 * @var \yii\web\View $this
 * @var \app\models\ChangePasswordForm $changePasswordForm
 */

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Change password';
?>
<div class="container style_external_links">
    <div class="content">

        <h1><?= Html::encode($this->title) ?></h1>
        <br>

        <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-6">

                    <?= $form->field($changePasswordForm, 'currentPassword')->passwordInput() ?>
                    <hr>
                    <?= $form->field($changePasswordForm, 'password')->passwordInput([
                        'autocomplete' => 'off',
                    ])->hint('The minimum length is 6 characters.') ?>
                    <?= $form->field($changePasswordForm, 'passwordRepeat')->passwordInput([
                        'autocomplete' => 'off',
                    ]) ?>

                </div>
            </div>

            <br>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>