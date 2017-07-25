<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $tutorials \app\models\Wiki[] */
?>
<div class="tutorials">
    <div class="dashed-heading-front-section">
        <span>Latest Tutorials</span>
    </div>
    <ul class="latest-list">
        <?php foreach ($tutorials as $tutorial): ?>
            <li><?= Html::a(Html::encode($tutorial->getLinkTitle()), Url::to($tutorial->getUrl()))?></li>
        <?php endforeach ?>
    </ul>
    <div class="row padded-row">
        <a href="<?= Url::to(['wiki/index']) ?>" class="btn btn-front btn-block">See all tutorials</a>
    </div>
</div>
