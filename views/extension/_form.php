<?php

use app\models\Extension;
use app\models\ExtensionCategory;
use dosamigos\selectize\SelectizeTextInput;
use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Extension */
/* @var $form ActiveForm */

?>
<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    // TODO ajax validation
]); ?>

<?php if ($model->isNewRecord) {
    echo Html::activeRadioList($model, 'from_packagist', [
        1 => '<strong>Import from <a href="https://packagist.org/about" target="_blank" rel="noopener noreferrer">Packagist</a></strong><p>Select this option if your extension is available on packagist. We will import most of the information from Packagist and Github.<br>This is the <strong>recommended</strong> option for Yii 2 extensions.</p>',
        0 => '<strong>Custom Description</strong><p>Select this option if you want to manage version information and description on yiiframework.com and not import anything.<br>This option is used for code that is not hosted on github and not installable via composer, mainly Yii 1.1 extensions.</p>',
    ], [
        'encode' => false,
        'separator' => '<br>',
        'itemOptions' => [
            'labelOptions' => ['class' => 'radiolist-label'],
        ],
        'id' => 'extension-packagist',
    ]);
} ?>

<noscript>
    <?= Alert::widget(['body' => 'Sorry, this page does not work without Javascript!', 'closeButton' => false, 'options' => ['class' => 'alert-danger']]) ?>
</noscript>
<div id="extension-form"<?php if ($model->from_packagist === null) echo ' style="display: none;"'; ?>>

    <div class="row">
        <div class="col-md-9">

            <?=$form->field($model, 'name', ['options' => ['class' => 'nopackagist']])
                    ->textInput(['disabled' => !$model->isNewRecord])
                    ->hint('Name must start with a letter and contain lower-case word characters only.<br>Name cannot be changed once the extension is created.');
            ?>

            <?= $form->field($model, 'packagist_url', ['options' => ['class' => 'packagist']])
                     ->textInput(['disabled' => !$model->isNewRecord])
                     ->error(['encode' => false])
                     ->hint('Enter the URL of the package registered on Packagist or the composer package name.<br>For example <code>http://packagist.org/p/yiisoft/yii2-redis</code> or <code>yiisoft/yii2-redis</code>.') ?>

            <?= $form->field($model, 'category_id')->dropDownList(ExtensionCategory::getSelectData(), ['prompt' => 'Please select...']) ?>
            <?= $form->field($model, 'yii_version', ['options' => ['class' => 'nopackagist']])
                ->dropDownList(['2.0.*' => 'Version 2.0', '1.1.*' => 'Version 1.1', '*' => 'Version independent'], ['prompt' => 'Please select...'])
                ->hint('Please select the Yii version for this article if the content is valid only for a specific version of Yii.')
            ?>
            <?= $form->field($model, 'license_id', ['options' => ['class' => 'nopackagist']])
                ->dropDownList(Extension::getLicenseSelect(), ['prompt' => 'Please select...'])
                ->hint('All extensions shared on yiiframework.com must be open source.<br>We encourage you to use one of the licenses listed in the above drop-down list. For more information you may visit <a href="https://choosealicense.com/" target="_blank" rel="noopener noreferrer">choosealicense.com</a>.');
            ?>

            <?= $form->field($model, 'github_url', ['options' => ['class' => 'nopackagist']])
                ->textInput()
                ->hint('If your code is hosted somewhere publicly for example on Github, enter the URL here.' . ($model->isNewRecord ? '<br>If your code is on github and packagist, consider importing details instead.' : '')) ?>

            <?= $form->field($model, 'tagline', ['options' => ['class' => 'nopackagist']])->textInput()->hint('A short summary') ?>


        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model, 'description', ['options' => ['class' => 'nopackagist']])->textarea(['class' => 'markdown-editor']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::class, [
                // calls an action that returns a JSON object with matched
                // tags
                'loadUrl' => ['extension/list-tags'],
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
            echo Html::a('Abort', $model->getUrl(), ['class' => 'btn btn-danger']);
        } ?>
    </div>

</div>

<?php ActiveForm::end(); ?>


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

if ($model->from_packagist === null) {
    $this->registerJs("$('.packagist').hide(); $('.nopackagist').hide();");
} elseif ($model->from_packagist == 1) {
    $this->registerJs("$('.packagist').show(); $('.nopackagist').hide();");
} else {
    $this->registerJs("$('.packagist').hide(); $('.nopackagist').show();");
}

$this->registerJs(<<<'JS'

    var packagistSelect = $('#extension-packagist');
    packagistSelect.on('click', function(e) {

        var extensionForm = $('#extension-form');

        var selected = packagistSelect.find('input[type=radio]:checked');
        if (selected.length == 0) {
            return;
        }
        extensionForm.show();
        if (selected.val() == '1') {
            $('.packagist').slideDown();
            $('.nopackagist').slideUp();
            console.log('packagist');
        } else {
            $('.packagist').slideUp();
            $('.nopackagist').slideDown();
            console.log('no packagist');
        }

        e.stopPropagation();
    });
JS
);


?>
