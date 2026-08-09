<?php

use app\components\UserPermissions;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/** @var $this View */
/** @var $roleUsers array */

?>
<header class="admin-page__header">
    <p>Site operations</p>
    <h1>Administration</h1>
</header>

<ul class="admin-dashboard">
<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_USERS)): ?>
    <li><?= Html::a('<strong>Users</strong><span>Manage accounts and permissions</span>', ['user-admin/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_NEWS)): ?>
    <li><?= Html::a('<strong>News</strong><span>Publish and maintain announcements</span>', ['news/admin']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_COMMENTS)): ?>
    <li><?= Html::a('<strong>Comments</strong><span>Review community discussion</span>', ['comment-admin/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_WIKI)): ?>
    <li><?= Html::a('<strong>Wiki</strong><span>Review and edit contributed articles</span>', ['wiki-admin/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_EXTENSIONS)): ?>
    <li><?= Html::a('<strong>Extensions</strong><span>Open the public extension manager</span>', ['extension/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_FORUM)): ?>
    <li><?= Html::a('<strong>Discourse</strong><span>Forum integration settings</span>', ['admin/discourse']) ?></li>
<?php endif?>

	<li><?= Html::a('<strong>Repository status</strong><span>View public package progress</span>', ['status/index']) ?></li>

</ul>

<h2 class="admin-page__section-title">RBAC assignments</h2>

<?php foreach($roleUsers as $role => $users): ?>

    <h3><?= Html::encode($role) ?></h3>

    <ul><li><?= implode('</li><li>', array_map(static function(User $user) {
        return $user->getRankLink();
    }, $users)) ?></li></ul>

<?php endforeach; ?>
