<?php

use app\models\WikiCategory;
use app\models\WikiTag;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

/** @var $dataProvider ActiveDataProvider */
/** @var $category WikiCategory */
/** @var $tag WikiTag */
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
        <div class="col-sm-9 col-md-10 col-lg-10" role="main">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_view',
                'itemOptions' => ['class' => 'col-xs-12 wiki-list-column'],
                'layout' => "{summary}\n<div class=\"row wiki-list-grid\">{items}</div>\n{pager}",
            ]) ?>
        </div>

        <div class="col-sm-3 col-md-2 col-lg-2 wiki-sidebar">
            <?= $this->render('_sidebar', [
                'category' => $category->id ?? null,
                'tag' => $tag,
                'sort' => $dataProvider->sort,
                'version' => $version,
            ]) ?>
        </div>
    </div>
</div>
