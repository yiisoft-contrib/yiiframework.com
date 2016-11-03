<?php
/**
 * @var \yii\web\View $this
 * @var integer $starValue
 * @var string $ajaxUrl
 */

use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<span class="star-wrapper">
    <?= Html::tag('span', '', [
        'class' => 'icon ' . ($starValue ? 'icon-star' : 'icon-star-empty'),
        'data-star-url' => $ajaxUrl
    ]) ?>
</span>
