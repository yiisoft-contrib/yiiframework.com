<?php
/**
 * @var \yii\base\View $this
 * @var string $queryString
 */

use yii\helpers\Html;

?>
<?= Html::beginForm('', 'get', ['class' => 'form-inline']); ?>
    <div class="form-group col-md-6">
        <?= Html::input('string', 'q', $queryString, [
            'autocomplete' => 'off',
            'placeholder' => 'Search extension...',
            'class' => 'form-control',
            'style' => 'width: 100%;'
        ]); ?>
    </div>
    <?= Html::submitButton('Search', ['class' => 'btn btn-info']);?>
<?= Html::endForm(); ?>
<br>