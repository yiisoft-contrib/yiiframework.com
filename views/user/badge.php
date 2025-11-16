<?php

use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var yii\web\View $this  */
/* @var app\models\Badge $badge  */
/* @var ActiveDataProvider $users  */
/* @var int $count */

$this->title = $badge->name . ' - Badges';

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, badges, ' . $badge->name]);

?>
<div class="container style_external_links view-user-view-badge">
    <div class="content">

        <div class="row">
            <div class="col-xs-1">
                <div class="userbadge-icon userbadge-<?= $badge->urlname ?>"></div>
            </div>
            <div class="col-xs-11">
                <h2><?= Html::encode($badge->name) ?> Badge</h2>
                <h3 class="description"><?= Html::encode($badge->description) ?></h3>
            </div>
        </div>

        <div class="info">
            <strong class="count"><?= $count ?></strong>
            users earned this badge. Recently awarded to:
        </div>
        <div class="g-list-view">
            <?= ListView::widget([
                'dataProvider'=>$users,
                'itemView' => 'badge_user',
            ]) ?>
        </div>

    </div>
</div>
