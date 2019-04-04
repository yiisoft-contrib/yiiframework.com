<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hall of Fame';

if (Yii::$app->user->can(\app\components\UserPermissions::PERMISSION_MANAGE_USERS)) {
    $this->beginBlock('adminNav');
    echo \yii\bootstrap4\Nav::widget([
        'id' => 'admin-nav',
        'items' => [
            ['label' => 'User Admin', 'url' => ['user-admin/index']],
        ],
    ]);
    $this->endBlock();
}

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, hall of fame']);

?>
<div class="container style_external_links">
    <div class="content">
        <h1>Hall of Fame</h1>

        <div class="row">
        <?php if ($this->beginCache('user/halloffame', ['duration' => 3600])) { ?>
            <div class="halloffame-members">
                <h3>Top Rated Members</h3>
                <ul>
                    <?php foreach (User::getTopUsers() as $model): ?>
                        <li><span><?=(int)$model->rating ?></span> <?= $model->rankLink ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="halloffame-members">
                <h3>Top Extension Developers</h3>
                <ul>
                    <?php foreach (User::getTopExtensionAuthors() as $model): ?>
                        <li><span><?= $model->extension_count ?></span> <?= $model->rankLink ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="halloffame-members">
                <h3>Top Wiki Authors</h3>
                <ul>
                    <?php foreach (User::getTopWikiAuthors() as $model): ?>
                        <li><span><?= $model->wiki_count ?></span> <?= $model->rankLink ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="halloffame-members">
                <h3>Top Comment Authors</h3>
                <ul>
                    <?php foreach (User::getTopCommentAuthors() as $model): ?>
                        <li><span><?= $model->comment_count ?></span> <?= $model->rankLink ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php $this->endCache();
        } ?>
        </div>

        <div class="halloffame-all-members"><?= Html::a('View all members', ['user/index']); ?></div>
    </div>
</div>
