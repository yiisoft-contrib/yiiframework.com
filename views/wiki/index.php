<?php

use yii\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $category \app\models\WikiCategory */
/** @var $tag \app\models\WikiTag */
/** @var $version string */


if ($category !== null) {
    $this->title = $category->name;
} else {
    $this->title = 'Wiki';
}

$this->beginBlock('contentSelectors');
    echo $this->render('partials/_versions', [
        'currentVersion' => $version,
        'category' => $category,
        'tag' => $tag,
    ]);
$this->endBlock();

?>
<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-2">
            <?= $this->render('_sidebar', [
                'category' => $category ? $category->id : null,
                'tag' => $tag,
                'sort' => $dataProvider->sort,
                'version' => $version,
            ]) ?>
        </div>

        <div class="col-sm-10" role="main">

            <h1>Wiki articles <small><?php
                if (!empty($category)) {
                    echo " in category " . Html::encode($category->name);
                }
                if ($tag !== null) {
                    echo ' tagged with "' . Html::encode($tag->name) . '"';
                }
                ?></small></h1>

	        <?php if (empty($category) && empty($tag)) {
	        	echo \app\widgets\SearchForm::widget([
			        'type' => \app\models\search\SearchActiveRecord::SEARCH_WIKI,
                    'version' => isset($version) ? $version : '2.0',
                    'placeholder' => 'Search Wiki…',
                ]);
			} ?>

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
            ]) ?>

<!--            <nav class="wiki-pagination-holder">
              <ul class="pagination pagination-lg wiki-pagination">
                <li class="prev disabled">
                    <span><i class="fa fa-chevron-left" aria-hidden="true"></i></span>
                </li>
                <li class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#">6</a></li>
                <li><a href="#">7</a></li>
                <li><a href="#">8</a></li>
                <li><a href="#">9</a></li>
                <li><a href="#">10</a></li>
                <li class="next">
                    <a href="#"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                </li>
              </ul>
            </nav>-->

        </div>
    </div>
</div>