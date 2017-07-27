<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $badge app\models\Badge */
/* @var $users \yii\data\ActiveDataProvider */

$this->title = $badge->name . ' - Badges';

echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => 'Badges',
    'menu' => [
        ['label' => 'User Admin', 'url' => ['user-admin/index'], 'visible' => Yii::$app->user->can('news:pAdmin') ],
    ]
]);

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, badges, ' . $badge->name]);

?>
<div class="container style_external_links view-user-view-badge">
    <div class="content">

        <div class="row">
            <div class="col-xs-1">
                <div class="userbadge-icon userbadge-<?= $badge->urlname ?>"></div>
            </div>
            <div class="col-xs-11">
                <h2><?= Html::encode($badge->name) ?></h2>
                <h3 class="description"><?= $badge->description ?></h3>
            </div>
        </div>

        <div class="info">
            <strong class="count"><?= $count ?></strong>
            users earned this badge. Recently awarded to:
        </div>
        <div class="g-list-view">
            <?= \yii\widgets\ListView::widget([
                'dataProvider'=>$users,
                'itemView' => 'badge_user',
            ]) ?>
        </div>

    </div>
</div>
