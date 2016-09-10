<?php

use app\models\News;
use dosamigos\selectize\SelectizeTextInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->dropDownList(News::getStatusList(), ['prompt' => 'Please choose...']) ?>

    <?= $form->field($model, 'news_date')->widget(\yii\jui\DatePicker::className(), [
        'options' => ['class' => 'form-control'],
    ]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::className(), [
        // calls an action that returns a JSON object with matched
        // tags
        'loadUrl' => ['news/list-tags'],
        'options' => ['class' => 'form-control'],
        'clientOptions' => [
            'plugins' => ['remove_button'],
            'valueField' => 'name',
            'labelField' => 'name',
            'searchField' => ['name'],
            'create' => true,
        ],
    ])->hint('Use commas to separate tags') ?>

    <?= $form->field($model, 'content')->label(false)->textarea(['rows' => 6, 'class' => 'markdown-editor'])->hint('Content will be converted using Guide Markdown') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
