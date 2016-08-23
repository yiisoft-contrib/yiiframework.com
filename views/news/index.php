<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $year int */

if ($year) {
	$this->title = "News from $year";
} else {
	$this->title = 'Latest News';
}
echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'News Admin', 'url' => ['news/admin'], 'visible' => Yii::$app->user->can('news:pAdmin') ],
    ]
]);
?>
<div class="container style_external_links">
    <div class="row">
        <div class="content">

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

					<?= \app\widgets\NewsArchive::widget() ?>

					<h2>Tags</h2>

					<p>TODO</p>

				</div>
			</div>

        </div>
    </div>
</div>
