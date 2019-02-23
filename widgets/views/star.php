<?php
/**
 * @var \yii\web\View $this
 * @var integer $starValue
 * @var string $ajaxUrl
 */

use yii\bootstrap\Html;

?>

<div class="star-wrapper">
    <?= Html::tag('span', '', [
        'class' => 'fa ' . ($starValue ? 'fa-star' : 'fa-star-o'),
        'data-star-url' => $ajaxUrl
    ]) ?>
    <?php if (isset($starCount)): ?>
        <span class="star-count"><?= (int) $starCount ?></span> follower<?= $starCount > 1 ? 's' : '' ?>
    <?php endif; ?>
</div>
