<?php

/* @var $this yii\web\View */

use app\widgets\SearchForm;
use yii\widgets\ListView;

/* @var $results yii\data\ActiveDataProvider */
/* @var $queryString string */
/* @var $language string */
/* @var $version string */
/* @var $type string */


$this->beginBlock('contentSelectors');
    echo $this->render('partials/_versions', [
        'searchQuery' => $queryString,
        'language' => $language,
        'version' => $version,
        'type' => $type,
    ]);
$this->endBlock();
?>
<div class="container">
    <div class="content">

        <?= SearchForm::widget([
            'type' => $type,
            'version' => $version,
            'language' => $language,
            'placeholder' => 'Search…',
            'value' => $queryString,
        ]) ?>

        <?= ListView::widget([
            'dataProvider' => $results,
            'itemView' => 'partials/_result',
        ]) ?>

    </div>
</div>
