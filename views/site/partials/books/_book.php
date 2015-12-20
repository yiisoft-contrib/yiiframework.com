<?php
use yii\helpers\Html;
?>
<div class="book">
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>"
                alt="<?= Html::encode($book['title'])?>"
                class="img-thumbnail img-responsive" width=300 />
        </div>
        <div class="col-md-8 col-sm-6 col-xs-12">
            <h2 class="media-heading"><?= Html::encode($book['title'])?></h2>
            <div class="author"> by <?= Html::encode($book['author'])?> </div>
            <p>
                <?= $book['description'] ?>
            </p>
            <div>
                <?= Html::a(Html::encode($book['title']), $book['url']) ?>
            </div>
            <div class="<?= Html::encode($book['level'])?>"><?= Html::encode($book['level-description'])?></div>
        </div>
    </div>
</div> <!-- book -->
