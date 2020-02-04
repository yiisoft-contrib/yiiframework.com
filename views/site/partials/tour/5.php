<?php
use yii\helpers\Html;
?>
<li class="glide__slide">
    <div class="row">
        <div class="col-md-7">
            <img src="<?= Yii::getAlias('@web/image/tour/5.png') ?>"
                alt=""
                class="img-fluid"/>
        </div>
        <div class="col-md-5">
            <div class="tour-subheader">
                Step 5
            </div>
            <div class="tour-header">
                Where to go next
            </div>
            <div class="tour-content">
                <p>Check these helpful resources:</p>

                <ul>
                    <li><?= Html::a('Documentation', ['guide/entry']) ?></li>
                    <li><?= Html::a('Community', ['site/community']) ?></li>
                    <li><?= Html::a('Extensions', ['extension/index']) ?></li>
                </ul>
            </div>
        </div>
    </div>
</li>
