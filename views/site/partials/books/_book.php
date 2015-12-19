<?php
use yii\helpers\Html;
?>
<div class="col-md-12">
    <div class="book media">
        <div class="row">
            <div class="col-md-4">
                <div class="media-left">
                    <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>"
                        alt="<?= Html::encode($book['title'])?>"
                        class="img-thumbnail media-object pull-left" width=300 />
                </div>

            </div>
            <div class="col-md-8">
                <div class="media-body">
                    <h2 class="media-heading"><?= Html::encode($book['title'])?></h2>
                    <div class="author"> by <?= Html::encode($book['author'])?> </div>
                    <p>
                        <?= Html::encode($book['description'])?>
                    </p>
                    <div>
                        <a href="<?= Html::encode($book['url'])?>"><?= Html::encode($book['title'])?></a>
                    </div>
                    <div class="<?= Html::encode($book['level'])?>"><?= Html::encode($book['level-description'])?></div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- book -->
