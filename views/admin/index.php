<?php

use app\components\UserPermissions;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/** @var $this View */
/** @var $roleUsers array */

?>
<h1>Admin</h1>


<ul>
<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_USERS)): ?>
    <li><?= Html::a('Manage Users', ['user-admin/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_NEWS)): ?>
    <li><?= Html::a('Manage News', ['news/admin']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_COMMENTS)): ?>
    <li><?= Html::a('Manage Comments', ['comment-admin/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_WIKI)): ?>
    <li><?= Html::a('Manage Wiki', ['wiki-admin/index']) ?></li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_EXTENSIONS)): ?>
    <li><?= Html::a('Manage Extensions', ['extension/index']) ?> (Currently no separate admin interface)</li>
<?php endif?>

<?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_FORUM)): ?>
    <li><?= Html::a('Discourse Forum Integration', ['admin/discourse']) ?></li>
<?php endif?>

	<li><?= Html::a('View Repository Status', ['status/index']) ?> (publicly accessable)

</ul>

<h2>RBAC Assignments</h2>

<?php foreach($roleUsers as $role => $users): ?>

    <h3><?= Html::encode($role) ?></h3>

    <ul><li><?= implode('</li><li>', array_map(static function(User $user) {
        return $user->getRankLink();
    }, $users)) ?></li></ul>

<?php endforeach; ?>

