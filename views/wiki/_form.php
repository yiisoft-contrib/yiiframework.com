<?php

use app\models\WikiCategory;
use dosamigos\selectize\SelectizeTextInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Wiki */
/* @var $form ActiveForm */

?>
<div class="wiki-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-9">

            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'content')->textarea() //TODO markdown editor! y-resizeable ?>

            <?php if (!$model->isNewRecord): ?>
                <?= $form->field($model, 'memo')->hint('Give a short summary of what you changed.') ?>
            <?php endif; ?>


        </div>
        <div class="col-md-3">

            <?= $form->field($model, 'category_id')->dropDownList(WikiCategory::getSelectData(), ['prompt' => 'Please select...']) ?>
            <?= $form->field($model, 'yii_version')
                ->dropDownList(['2.0' => 'Version 2.0', '1.1' => 'Version 1.1', 'all' => 'Version independent'], ['prompt' => 'Please select...'])
                ->hint('Please select the Yii version for this article if the content is valid only for a specific version of Yii.')
            ?>

            <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::className(), [
                // calls an action that returns a JSON object with matched
                // tags
                'loadUrl' => ['wiki/list-tags'],
                'options' => ['class' => 'form-control'],
                'clientOptions' => [
                    'plugins' => ['remove_button'],
                    'valueField' => 'name',
                    'labelField' => 'name',
                    'searchField' => ['name'],
                    'create' => true,
                ],
            ])->hint('Use commas to separate tags') ?>

        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        <a class="g-markdown-preview" href="#" rel="#wiki_content">Preview</a>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- wiki-_form -->



<?php //TODO JS for keeping session alive? Yii::app()->clientScript->registerScript('keepAlive', 'site.keepAlive();', CClientScript::POS_READY); ?>
