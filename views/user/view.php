<?php

use app\components\UserPermissions;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $userCount int */
/* @var $wikis app\models\Wiki[] */
/* @var $extensions app\models\Extension[] */

$this->title = $model->display_name . "'s profile";
$forumUrl = $model->getForumUrl();
$avatarUrl = $model->hasAvatar()
    ? $model->getAvatarUrl()
    : Yii::getAlias('@web/image/user/default_user.svg');

if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_USERS)) {
    $this->beginBlock('adminNav');
    echo Nav::widget([
        'id' => 'admin-nav',
        'items' => [
            ['label' => 'User Admin', 'url' => ['user-admin/index'], 'visible' => Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_USERS) ],
            ['label' => 'View User as Admin', 'url' => ['user-admin/view', 'id' => $model->id], 'visible' => Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_USERS) ],
        ],
    ]);
    $this->endBlock();
}

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, members']);

?>
<div class="container style_external_links">
    <div class="content user-public-profile">

        <header class="user-profile-hero">
            <img class="user-profile-hero__avatar" src="<?= Html::encode($avatarUrl) ?>" alt="">
            <div class="user-profile-hero__identity">
                <h1><?= Html::encode($model->display_name) ?></h1>
                <p class="user-profile-hero__username">@<?= Html::encode($model->username) ?></p>
                <p class="user-profile-hero__joined">Member since <?= Yii::$app->formatter->asDate($model->created_at) ?></p>
            </div>
        </header>

        <section class="user-profile-stats" aria-label="Community activity">
            <div class="user-profile-stat">
                <span class="user-profile-stat__value"><?= (int) $model->rating ?></span>
                <span class="user-profile-stat__label">Rating</span>
                <?php if ($model->rank == 999999): ?>
                    <span class="user-profile-stat__note">Not ranked</span>
                <?php else: ?>
                    <?= Html::a(
                        'No. ' . (int) $model->rank . ' of ' . (int) $userCount,
                        ['user/index', 'sort' => 'rank', 'page' => (int) (($model->rank - 1) / 50) + 1],
                        ['class' => 'user-profile-stat__note']
                    ) ?>
                <?php endif; ?>
            </div>
            <div class="user-profile-stat">
                <span class="user-profile-stat__value"><?= (int) $model->post_count ?></span>
                <span class="user-profile-stat__label">Forum posts</span>
                <?php if ($forumUrl): ?>
                    <?= Html::a('View profile', $forumUrl, ['class' => 'user-profile-stat__note']) ?>
                <?php endif; ?>
            </div>
            <div class="user-profile-stat">
                <span class="user-profile-stat__value"><?= (int) $model->extension_count ?></span>
                <span class="user-profile-stat__label">Extensions</span>
            </div>
            <div class="user-profile-stat">
                <span class="user-profile-stat__value"><?= (int) $model->wiki_count ?></span>
                <span class="user-profile-stat__label">Wiki articles</span>
            </div>
            <div class="user-profile-stat">
                <span class="user-profile-stat__value"><?= (int) $model->comment_count ?></span>
                <span class="user-profile-stat__label">Comments</span>
            </div>
        </section>

        <?php if (!empty($extensions) || !empty($wikis)): ?>
            <div class="user-profile-contributions">
                <?php if (!empty($extensions)): ?>
                    <section>
                        <h2>Extensions</h2>
                        <ul class="user-profile-links">
                            <?php foreach ($extensions as $extension): ?>
                                <li><?= Html::a(Html::encode($extension->getLinkTitle()), $extension->getUrl()) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <?php if (!empty($wikis)): ?>
                    <section>
                        <h2>Wiki Articles</h2>
                        <ul class="user-profile-links">
                            <?php foreach ($wikis as $wiki): ?>
                                <li><?= Html::a(Html::encode($wiki->getLinkTitle()), $wiki->getUrl()) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($model->badges)): ?>

            <h2>Badges</h2>
           <ul class="list-unstyled user-profile-badges">
                <?php foreach($model->getBadges()->with('badge')->all() as $info): ?>
                <?php
                    if (!$info->badge->active) {
                        continue;
                    }

                   if($info->complete_time) {
                       $title = sprintf('%s earned this badge on %s', Html::encode($model->display_name), Yii::$app->formatter->asDate($info->complete_time));
                   } else {
                       $title = sprintf('%s started this badge on %s', Html::encode($model->display_name), Yii::$app->formatter->asDate($info->create_time));
                   }
               ?>
                   <li>
                       <div class="userbadge">
                           <?php $percent = min(100, $info->progress); ?>
                           <div class="userbadge-icon userbadge-<?= $info->badge->urlname ?>" title="<?= $title ?>"></div>
                           <div class="userbadge-progress-bar" style="width: <?= round($percent) ?>%"></div>
                           <div class="userbadge-info">
                               <h3><?= Html::a(Html::encode($info->badge->name), ['user/view-badge', 'name' => $info->badge->urlname]) ?></h3>
                               <p><?= Html::encode($info->badge->description) ?></p>
                               <?php if ($info->complete_time): ?>
                                   <span class="percent">Earned: <span class="date"><?= Yii::$app->formatter->asRelativeTime($info->complete_time) ?></span></span>
                               <?php else: ?>
                                   <span class="percent">In progress (<?= round($percent)?>%)</span>
                               <?php endif ?>
                           </div>
                       </div>
                   </li>
               <?php endforeach ?>
           </ul>

        <?php endif ?>

    </div>
</div>
