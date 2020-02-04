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

            <h1>Wiki articles
                <small><?php
                    if (!empty($category)) {
                        echo " in category " . Html::encode($category->name);
                    }
                    if ($tag !== null) {
                        echo ' tagged with "' . Html::encode($tag->name) . '"';
                    }
                    ?></small>
            </h1>

            <?php if (empty($category) && empty($tag)) {
                echo \app\widgets\SearchForm::widget([
                    'type' => \app\models\search\SearchActiveRecord::SEARCH_WIKI,
                    'version' => isset($version) ? $version : '2.0',
                    'placeholder' => 'Search Wiki…',
                ]);
            } ?>

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'pager' => [
                    'class' => \yii\bootstrap4\LinkPager::class
                ],
                'itemView' => '_view',
            ]) ?>

        </div>
    </div>
</div>