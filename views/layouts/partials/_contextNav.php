<?php

/** @var $this yii\web\View */

?>
<div class="context-nav">
    <div class="container">
        <?php if (!empty($this->blocks['adminNav'])): ?>
            <div class="context-nav__admin"><?= $this->blocks['adminNav'] ?></div>
        <?php endif; ?>
        <?php if (!empty($this->blocks['contentSelectors'])): ?>
            <div class="context-nav__selectors"><?= $this->blocks['contentSelectors'] ?></div>
        <?php endif; ?>
    </div>
</div>
