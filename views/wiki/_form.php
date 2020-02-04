<?php

use app\models\Wiki;
use app\models\WikiCategory;
use dosamigos\selectize\SelectizeTextInput;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Wiki */
/* @var $form ActiveForm */

?>
<div class="wiki-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-9">

            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'content')->textarea(['class' => 'markdown-editor']) ?>

            <?php if (!$model->isNewRecord): ?>
                <?= $form->field($model, 'memo')->hint('Give a short summary of what you changed.') ?>
            <?php endif; ?>


        </div>
        <div class="col-md-3">

            <?= $form->field($model, 'category_id')->dropDownList(WikiCategory::getSelectData(), ['prompt' => 'Please select...']) ?>
            <?= $form->field($model, 'yii_version')
                ->dropDownList(Wiki::getYiiVersionOptions(), ['prompt' => 'Please select...'])
                ->hint('Please select the Yii version for this article if the content is valid only for a specific version of Yii.')
            ?>

            <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::class, [
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
        <?php if ($model->isNewRecord) {
            echo Html::a('Abort', ['index'], ['class' => 'btn btn-danger']);
        } else {
            echo Html::a('Abort', ['view', 'id' => $model->id, 'name' => $model->slug], ['class' => 'btn btn-danger']);
        } ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- wiki-_form -->

<?php

// register a JS function that repeatedly calls the server to keep the session alive
// this prevents issues with users getting logged out when editing wiki for long time,
$this->registerJs(<<<JS
    window.setInterval(function()
    {
        $.get(yiiBaseUrl + '/wiki/keep-alive');
        // TODO show a nice warning when user go logged out and allow log in in other window before submitting the form
    }, 300000 /* call every 5 min */);
JS
);

?>
