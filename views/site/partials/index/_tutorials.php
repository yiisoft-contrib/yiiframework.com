<?php

use app\models\Wiki;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $tutorials Wiki[] */
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
</div>
