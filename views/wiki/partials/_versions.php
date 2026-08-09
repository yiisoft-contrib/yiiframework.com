<?php

/**
 * @var $this yii\web\View
 * @var $currentVersion string
 * @var $category app\models\WikiCategory|null
 * @var $tag app\models\WikiTag|null
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
