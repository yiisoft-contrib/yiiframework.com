<?php
use yii\helpers\Html;
?>
<div class="col-sm-3 col-md-3 col-lg-3">
    <div class="info-card">
        <div class="ribbon <?= Html::encode($book['level'])?>"><span><?= Html::encode($book['level-text'])?></span></div>
        <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>" alt="<?= Html::encode($book['title'])?>" />
        <div class="info-card-details animate">
            <div class="info-card-header">
                <h1><?= Html::encode($book['title'])?></h1>
                <h3> by <?= Html::encode($book['author'])?> </h3>
            </div>
            <div class="info-card-detail">
                <p>
                    <?= Html::encode($book['description'])?>
                </p>
            </div>
            <div class="social">
                <div>
                    <a href="<?= Html::encode($book['url'])?>"><?= Html::encode($book['title'])?></a>
                </div>
                <?= Html::encode($book['level-description'])?>
            </div>
        </div>
    </div>
</div> <!-- book -->
