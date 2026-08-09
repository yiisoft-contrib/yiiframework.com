<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $currentVersion string */

$versions = [
    '3.0' => 'Yii 3',
    '2.0' => 'Yii 2',
    '1.1' => 'Yii 1.1',
];
?>
<nav class="version-selector" aria-label="Yii version">
    <span class="version-selector__label">Version</span>
    <div class="version-selector__options">
        <?php foreach ($versions as $version => $label): ?>
            <?= Html::a($label, ['site/download', 'version' => $version], [
                'class' => 'version-selector__option' . ($version === $currentVersion ? ' is-active' : ''),
                'aria-current' => $version === $currentVersion ? 'page' : null,
            ]) ?>
        <?php endforeach; ?>
    </div>
</nav>
