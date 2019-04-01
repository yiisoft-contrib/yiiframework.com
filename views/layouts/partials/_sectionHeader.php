<?php

use yii\bootstrap4\Html;

/** @var $this yii\web\View */
/** @var $language string custom language tag for styling elements language dependent (optional, default 'en') */
/** @var $title string|array section title */
/** @var $selectors string the HTML content of the language and version selector dropdowns */

?>
<div class="section-header-wrap">
    <div class="container section-header lang-<?= $language ?? 'en' ?>">
        <div class="row">
            <div class="col-12 <?php if (empty($this->blocks['adminNav'])): echo 'col-md-6'; else: echo 'col-md-7'; endif; ?>">
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
                <div class="col-12 col-md-5 admin-nav">
                    <?= $this->blocks['adminNav'] ?>
                </div>
            <?php endif; ?>
			<?php if (!empty($this->blocks['contentSelectors'])): ?>
                <div class="col-12 col-md-offset-7 col-md-6 align-self-end">
                    <?= $this->blocks['contentSelectors'] ?>
                </div>
			<?php endif; ?>
        </div>
    </div>
</div>
