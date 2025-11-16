<?php

use yii\bootstrap\Html;

/** @var $this yii\web\View */
/** @var $language string custom language tag for styling elements language dependent (optional, default 'en') */
/** @var $title string|array section title */
/** @var $selectors string the HTML content of the language and version selector dropdowns */

?>
<div class="section-header-wrap">
    <div class="container section-header lang-<?= $language ?? 'en' ?>">
        <div class="row headline-row">
            <div class="col-xs-12 <?php if (empty($this->blocks['adminNav'])): echo 'col-md-12'; else: echo 'col-md-7'; endif; ?>">
                <div class="section-headline"><?php
                    if (is_array($title)) {
                        $items = [];
                        foreach($title as $name => $url) {
                            $items[] = Html::a(Html::encode($name), $url);
                        }
                        echo implode(' - ', $items);
                    } else {
                        echo Html::encode($title);
                    }
                ?></div>
            </div>
            <?php if (!empty($this->blocks['adminNav'])): ?>
                <div class="col-xs-12 col-md-5 admin-nav">
                    <?= $this->blocks['adminNav'] ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($this->blocks['contentSelectors'])): ?>
        <div class="row content-selector-row">
            <div class="col-xs-12 col-md-offset-7 col-md-5">
                <?= $this->blocks['contentSelectors'] ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
