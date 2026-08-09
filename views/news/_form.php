<?php

use app\models\News;
use dosamigos\selectize\SelectizeTextInput;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="news-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'news-editor-form']]); ?>

    <div class="news-editor-form__meta">
        <?= $form->field($model, 'status')->dropDownList(News::getStatusList(), ['prompt' => 'Choose a status']) ?>

        <?= $form->field($model, 'news_date')->widget(DatePicker::class, [
            'options' => ['class' => 'form-control'],
        ]) ?>
    </div>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::class, [
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

    <?= $form->field($model, 'content')->label('Content')->textarea([
        'rows' => 10,
        'class' => 'markdown-editor',
        'placeholder' => 'Write the news article in Markdown…',
    ])->hint('Markdown is supported and rendered using the guide formatter.') ?>

    <div class="news-editor-form__actions">
        <?= Html::a('Cancel', $model->isNewRecord
            ? ['news/admin']
            : ['news/view', 'id' => $model->id, 'name' => $model->slug], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Create news' : 'Save changes', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
