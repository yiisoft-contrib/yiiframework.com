<?php

use yii\bootstrap\Html;

/** @var $this yii\web\View */
/** @var $language string custom language tag for styling elements language dependent (optional, default 'en') */
/** @var $title string section title */
/** @var $selectors string the HTML content of the language and version selector dropdowns */

?>
<div class="section-header-wrap">
    <div class="container section-header lang-<?= isset($language) ? $language : 'en' ?>">
        <div class="row">
            <div class="col-xs-12 col-md-7">
                <div class="section-headline"><?= Html::encode($title) ?></div>
            </div>
            <?php if (!empty($this->blocks['adminNav'])): ?>
                <div class="col-xs-12 col-md-5 admin-header">
                    <?= $this->blocks['adminNav'] ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($this->blocks['contentSelectors'])): ?>
        <div class="row">
            <div class="col-xs-12 col-md-offset-7 col-md-5">
                <?= $this->blocks['contentSelectors'] ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
