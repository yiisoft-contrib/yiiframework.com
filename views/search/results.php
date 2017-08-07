<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

/* @var $results yii\data\ActiveDataProvider */
/* @var $queryString string */
/* @var $language string */
/* @var $version string */

$this->title = 'Search results for "' . Html::encode($queryString) . '"';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="guide-header-wrap">
    <div class="container guide-header lang-<?= $language ?>" xmlns="http://www.w3.org/1999/xhtml">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="guide-headline"><?= Html::encode($this->title) ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-offset-7 col-md-5">
                <?= $this->render('partials/_versions', [
                    'searchQuery' => $queryString,
                    'language' => $language,
                    'version' => $version,
                ])?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="content">

        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $results,
            'itemView' => 'partials/_result',
        ]) ?>

    </div>
</div>
