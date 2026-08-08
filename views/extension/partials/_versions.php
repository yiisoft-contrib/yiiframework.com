<?php

/**
 * @var $this yii\web\View
 * @var $currentVersion string
 * @var $category string
 * @var $tag app\models\ExtensionTag|null
 */

use app\models\Extension;
use yii\helpers\Html;

?>
<nav class="version-selector" aria-label="Yii version">
    <span class="version-selector__label">Version</span>
    <div class="version-selector__options">
        <?php foreach (Extension::getYiiVersionOptions() as $version => $label): ?>
            <?php
            $url = ['extension/index', 'version' => $version];
            if ($category) {
                $url['category'] = $category;
            }
            if ($tag) {
                $url['tag'] = $tag->slug;
            }
            ?>
            <?= Html::a($label, $url, [
                'class' => 'version-selector__option' . ($version === $currentVersion ? ' is-active' : ''),
                'aria-current' => $version === $currentVersion ? 'page' : null,
            ]) ?>
        <?php endforeach; ?>
    </div>
</nav>
