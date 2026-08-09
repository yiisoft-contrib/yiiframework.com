<?php

use app\components\UserPermissions;
use app\models\NewsTag;
use app\widgets\NewsArchive;
use app\widgets\NewsTaglist;
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
<div class="container style_external_links news-index">
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
			<div class="col-md-3 news-sidebar">

				<div class="panel panel-default news-rss-callout">
					<div class="panel-body">
                        <?= Html::a(
                            '<svg viewBox="0 0 20 20" aria-hidden="true">'
                            . '<path d="M3 2a1 1 0 0 0 0 2c7.18 0 13 5.82 13 13a1 1 0 1 0 2 0C18 8.716 11.284 2 3 2Zm0 5a1 1 0 0 0 0 2 8 8 0 0 1 8 8 1 1 0 1 0 2 0A10 10 0 0 0 3 7Zm2 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>'
                            . '</svg>',
                            ['rss/all'],
                            ['class' => 'news-rss-callout__icon', 'aria-label' => 'RSS Feed']
                        ) ?>
                        <span>Get notified of news as soon as they are available using our <?= Html::a('RSS Feed', ['rss/all']) ?>.</span>
					</div>
				</div>

				<?= NewsArchive::widget(['urlParams' => $urlParams]) ?>

				<?= NewsTaglist::widget(['urlParams' => $urlParams]) ?>

			</div>
		</div>

    </div>
</div>
