<?php
/**
 * @var \yii\base\View $this
 * @var string $queryString
 */

use yii\helpers\Html;

?>
<?= Html::beginForm(['extension/index'], 'get', ['class' => 'form-inline']) ?>
    <div class="form-group">
        <?= Html::input('string', 'q', $queryString, [
            'autocomplete' => 'off',
            'placeholder' => 'Search extension...',
            'class' => 'form-control',
        ]) ?>
    </div>
    <?= Html::submitButton('Search', ['class' => 'btn btn-info']) ?>
<?= Html::endForm() ?>
<br>