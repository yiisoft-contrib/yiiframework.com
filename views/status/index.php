<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var \yii\data\ArrayDataProvider $dataProvider */
/** @var string $version */
/** @var string[] $versions */
?>

<?php

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions', [
    'currentVersion' => $version,
    'versions' => $versions
]);
$this->endBlock();

?>

<div class="container">
    <div class="content">

        <?= GridView::widget([
            'layout' => "{items}\n{pager}",
            'tableOptions' => [
                'class' => 'table',
            ],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'repository',
                    'content' => function ($model) {
                        return Html::a($model['repository'], 'https://github.com/' . $model['repository'] . '/');
                    }
                ],
                'latest',
                [
                    'attribute' => 'no_release_for',
                    'value' => function ($model) {
                        if ($model['no_release_for'] === null) {
                            return '';
                        }

                        return $model['no_release_for'] . ($model['no_release_for'] == 1 ? ' day' : ' days');
                    }
                ],
                [
                    'attribute' => 'issues',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model['issues'] > 0 ? Html::a($model['issues'], 'https://github.com/' . $model['repository'] . '/issues') : 0;
                    }
                ],
                [
                    'attribute' => 'pullRequests',
                    'label' => 'PRs',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model['pullRequests'] > 0 ? Html::a($model['pullRequests'], 'https://github.com/' . $model['repository'] . '/pulls') : 0;
                    }
                ],
                'status:image',
                [
                    'attribute' => 'diff',
                    'content' => function ($model) {
                        $parts = explode('/', $model['diff']);
                        return Html::a(Html::encode(end($parts)), $model['diff']);
                    }
                ],
            ],
        ]) ?>
    </div>
</div>