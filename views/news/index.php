<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $year int */
/* @var $tag \app\models\NewsTag */

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

if (Yii::$app->user->can('news:pAdmin')) {
	$this->beginBlock('adminNav');
	echo \yii\bootstrap\Nav::widget([
		'id' => 'admin-nav',
		'items' => [
			['label' => 'News Admin', 'url' => ['news/admin'], 'visible' => Yii::$app->user->can('news:pAdmin') ],
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

				<?= \app\widgets\NewsArchive::widget(['urlParams' => $urlParams]) ?>

				<?= \app\widgets\NewsTaglist::widget(['urlParams' => $urlParams]) ?>


				<div class="panel panel-default">
					<div class="panel-heading">
						<strong>Yii Feed</strong>
					</div>
					<div class="panel-body">
						This site contains official framework annoncements only.
						Find more Yii related news on <a href="http://yiifeed.com/">yiifeed.com</a>.
					</div>
				</div>

			</div>
		</div>

    </div>
</div>
