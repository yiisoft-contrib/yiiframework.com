<?php

use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var ArrayDataProvider $dataProvider */
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
            'rowOptions'=> static function ($model) {
                if ($model['no_release_for'] === null && !$model['optionalForFrameworkAnnounce']) {
                    return ['class' => 'warning'];
                }

                return [];
            },
            'columns' => [
                [
                    'label' => '#',
                    'value' => static function ($model, $key, $index) {
                        return $index + 1;
                    },
                    'visible' => $version === '3.0',
                ],
                [
                    'attribute' => 'repository',
                    'content' => static function ($model) {
                        return Html::a($model['repository'], 'https://github.com/' . $model['repository'] . '/');
                    }
                ],
                'latest',
                [
                    'attribute' => 'no_release_for',
                    'value' => static function ($model) {
                        if ($model['no_release_for'] === null) {
                            return '';
                        }

                        return $model['no_release_for'] . ($model['no_release_for'] == 1 ? ' day' : ' days');
                    }
                ],
                [
                    'attribute' => 'issues',
                    'format' => 'html',
                    'value' => static function ($model) {
                        return $model['issues'] > 0 ? Html::a($model['issues'], 'https://github.com/' . $model['repository'] . '/issues') : 0;
                    }
                ],
                [
                    'attribute' => 'pullRequests',
                    'label' => 'PRs',
                    'format' => 'html',
                    'value' => static function ($model) {
                        return $model['pullRequests'] > 0 ? Html::a($model['pullRequests'], 'https://github.com/' . $model['repository'] . '/pulls') : 0;
                    }
                ],
                'status:image',
                'githubStatus:image',
                'coverage:image',
                'quality:image',
                [
                    'attribute' => 'diff',
                    'content' => static function ($model) {
                        $parts = explode('/', $model['diff']);

                        $changeLog = '';

                        if (!empty($model['mergedSinceRelease'])) {
                            $label = count($model['mergedSinceRelease']) . ' PRs';

                            $link = 'https://github.com/' . $model['repository'] . '/blob/master/';
                            $link .= $model['repository'] === 'yiisoft/yii2' ? 'framework/CHANGELOG.md' : 'CHANGELOG.md';

                            $title = [];
                            foreach ($model['mergedSinceRelease'] as $pr) {
                                $title[] = '#' . $pr['number'] . ': ' . $pr['title'];
                            }

                            $changeLog = ' (' . Html::a($label,
                                $link,
                                ['title' => implode("\n", $title)]
                            ) . ')';
                        }

                        return Html::a(Html::encode(end($parts)), $model['diff']) . $changeLog;
                    }
                ],
                [
                    'attribute' => 'optionalForFrameworkAnnounce',
                    'content' => static function ($model) {
                        if ($model['repository'] === 'yiisoft/yii' && str_contains($model['repository'], 'yii2')) {
                            return '';
                        }

                        return $model['optionalForFrameworkAnnounce'] ? 'Yes' : '';
                    },
                    'visible' => $version === '3.0',
                ],
            ],
        ]) ?>
    </div>
</div>
