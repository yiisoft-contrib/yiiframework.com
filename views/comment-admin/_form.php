<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-form">

    by <?= $model->user->rankLink ?? User::DELETED_USER_HTML ?> at
    <span class="date"><?=Yii::$app->formatter->format($model->created_at, 'datetime')?></span>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'text')->textarea(['class' => 'markdown-editor'])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
