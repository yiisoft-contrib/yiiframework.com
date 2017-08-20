<?php

use yii\helpers\Html;

/** @var $dataProvider \yii\data\ActiveDataProvider */
/** @var $category \app\models\WikiCategory */
/** @var $tag \app\models\WikiTag */


$this->title = 'Wiki';

if ($category === null) {
    $this->title .= ' articles';
} else {
    $this->title .= ' ' . $category->name;
}

if ($tag !== null) {
    $this->title .= ' taggeed with "' . $tag->name . '"';
}
$this->params['breadcrumbs'][] = $this->title;

$this->beginBlock('contentSelectors');
echo 'TODO add version selector';
$this->endBlock();

?>
<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                'category' => $category ? $category->id : null,
                'tag' => $tag,
                'sort' => $dataProvider->sort,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

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