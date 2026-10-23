<?php

/** @var yii\web\View $this */

use app\widgets\SearchForm;
use yii\helpers\Html;
use yii\widgets\ListView;

/** @var yii\data\ActiveDataProvider $results */
/** @var string $queryString */
/** @var string $language */
/** @var string $version */
/** @var string $type */


$this->beginBlock('contentSelectors');
    echo $this->render('partials/_versions', [
        'searchQuery' => $queryString,
        'language' => $language,
        'version' => $version,
        'type' => $type,
    ]);
$this->endBlock();
?>
<main class="container search-page">
    <div class="content search-page__content">
        <header class="search-page__header">
            <p>Search</p>
            <h1><?= $queryString === '' ? 'Find Yii resources' : 'Results for “' . Html::encode($queryString) . '”' ?></h1>
        </header>

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
</main>
