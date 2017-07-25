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
<div class="container style_external_links">
    <div class="content">

        <div class="userbadge-icon userbadge-<?php echo $badge->urlname ?>">
        </div>
        <h2><?php echo Html::encode($badge->name) ?></h2>
        <h3 class="description"><?php echo $badge->description ?></h3>

        <div class="info">
            <strong class="count"><?php echo $count ?></strong>
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
