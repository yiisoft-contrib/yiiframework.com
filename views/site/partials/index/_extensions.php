<?php
/* @var $extensions \app\models\Extension[] */

use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="extensions">
    <div class="dashed-heading-front-section">
        <span>Latest Extensions</span>
    </div>
    <ul class="latest-list">
        <?php foreach ($extensions as $extension): ?>
        <li>
            <?= Html::a(Html::encode($extension->name), ['extension/view', 'name' => $extension->name]) ?>
            <p><?= Html::encode($extension->tagline) ?></p>
        </li>
        <?php endforeach ?>
    </ul>
    <div class="row padded-row">
        <a href="<?= Url::to(['extension/index']) ?>" class="btn btn-front btn-block">See all extensions</a>
    </div>
</div>
