<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hall of Fame';

echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'User Admin', 'url' => ['user-admin/index'], 'visible' => Yii::$app->user->can('news:pAdmin') ],
    ]
]);

$this->registerMetaTag(['name' => 'keywords', 'value' => 'yii framework, community, hall of fame']);

?>
<div class="container style_external_links">
    <div class="content">

	<?php if($this->beginCache('user/halloffame',array('duration'=>3600))) { ?>
	<div class="grid_3 alpha">
		<div class="members">
			<h2>Top Rated Members</h2>
			<ul>
				<?php foreach(User::getTopUsers() as $model): ?>
				<li><span><?php echo ((int) $model->rating); ?></span> <?php echo $model->rankLink; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="grid_3">
		<div class="members">
			<h2>Top Extension Developers</h2>
			<ul>
				<?php foreach(User::getTopExtensionAuthors() as $model): ?>
				<li><span><?php echo $model->extension_count; ?></span> <?php echo $model->rankLink; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="grid_3">
		<div class="members">
			<h2>Top Wiki Authors</h2>
			<ul>
				<?php foreach(User::getTopWikiAuthors() as $model): ?>
				<li><span><?php echo $model->wiki_count; ?></span> <?php echo $model->rankLink; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="grid_3 omega">
		<div class="members">
			<h2>Top Comment Authors</h2>
			<ul>
				<?php foreach(User::getTopCommentAuthors() as $model): ?>
				<li><span><?php echo $model->comment_count; ?></span> <?php echo $model->rankLink; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<?php $this->endCache(); } ?>

	<div class="clear"></div>

	<div class="all-members"><?php echo Html::a('View all members',array('user/index')); ?></div>

    </div>
</div>
