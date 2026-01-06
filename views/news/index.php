<?php

use app\components\UserPermissions;
use app\models\NewsTag;
use app\widgets\NewsArchive;
use app\widgets\NewsTaglist;
use app\widgets\SearchForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $year int */
/* @var $tag NewsTag */

$urlParams = [];

if ($year) {
	$urlParams['year'] = $year;
	$this->title = "News from $year";
} else {
	$this->title = 'Latest News';
}
if ($tag) {
	$urlParams['tag'] = $tag->slug;
	$this->title .= ' tagged with "' . Html::encode($tag->name) . '"';
}

if (UserPermissions::canManageNews()) {
	$this->beginBlock('adminNav');
	echo Nav::widget([
		'id' => 'admin-nav',
		'items' => [
			['label' => 'News Admin', 'url' => ['news/admin']],
		],
	]);
	$this->endBlock();
}

?>
<div class="container style_external_links">
    <div class="content news-content">

        <div class="row">
			<div class="col-md-9">

				<?php //echo $this->render('_search', ['model' => $searchModel]); ?>

	            <?= ListView::widget([
	                'dataProvider' => $dataProvider,
	                'itemOptions' => ['class' => 'item'],
	                'itemView' => '_view',
					'summary' => '',
	            ]) ?>

			</div>
			<div class="col-md-3">


				<?= SearchForm::widget([
					'type' => 'news',
					'placeholder' => 'Search News…',
				]) ?>

				<?= NewsArchive::widget(['urlParams' => $urlParams]) ?>

				<?= NewsTaglist::widget(['urlParams' => $urlParams]) ?>

				<div class="panel panel-default">
					<div class="panel-heading">
						<strong>RSS Feed</strong>
					</div>
					<div class="panel-body">
						Get notified of news as soon as they are available by
						using our <?= Html::a('RSS Feed', ['rss/all'])?>.
					</div>
				</div>

			</div>
		</div>

    </div>
</div>
