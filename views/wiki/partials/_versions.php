<?php

/**
 * @var yii\web\View $this
 * @var string $currentVersion
 * @var app\models\WikiCategory|null $category
 * @var app\models\WikiTag|null $tag
 */

use app\models\Wiki;
use yii\helpers\Html;

?>
<nav class="version-selector" aria-label="Yii version">
    <span class="version-selector__label">Version</span>
    <div class="version-selector__options">
        <?php foreach (Wiki::getYiiVersionOptions() as $version => $label): ?>
            <?php
            $url = ['wiki/index', 'version' => $version];
            if ($category) {
                $url['category'] = $category->id;
            }
            if ($tag) {
                $url['tag'] = $tag->name;
            }
            ?>
            <?= Html::a($label, $url, [
                'class' => 'version-selector__option' . ($version === $currentVersion ? ' is-active' : ''),
                'aria-current' => $version === $currentVersion ? 'page' : null,
            ]) ?>
        <?php endforeach; ?>
    </div>
</nav>
