<?php
/**
 * @var yii\web\View $this
 * @var \app\components\packagist\Package $package
 * @var null|array $selectedVersion
 * @var array $selectedVersionData
 * @var array $versions
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = $package->getName();
$this->params['breadcrumbs'][] = ['label' => 'Extension', 'url' => ['extension/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($package->getName()) ?></h1>
                <small><?= Html::encode($package->getDescription()) ?></small>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <br>
    <?= \app\widgets\Alert::widget() ?>
    <?php if ($package): ?>
        <p><code class="hljs json language-json">composer require <?= Html::encode($package->getName()) ?></code></p>

        <?= DetailView::widget([
            'model' => $package,
            'attributes' => [
                'name' => [
                    'attribute' => 'name',
                    'value' => $package->getName(),
                ],
                'description' => [
                    'attribute' => 'description',
                    'value' => $package->getDescription(),
                ],
                [
                    'attribute' => 'repository',
                    'format' => 'raw',
                    'value' => Html::a($package->getRepositoryHost(), $package->getRepository(), ['target' => '_blank', 'rel' => 'noopener noreferrer'])
                ],
                [
                    'attribute' => 'downloads',
                    'format' => 'raw',
                    'value' => DetailView::widget([
                        'model' => $package->getDownloads(),
                        'attributes' => [
                            'total',
                            'monthly',
                            'daily'
                        ]
                    ])
                ],
                'favers' => [
                    'attribute' => 'favers',
                    'value' => $package->getFavers(),
                ],
                [
                    'attribute' => 'updatedAt',
                    'label' => 'Update time',
                    'format' => 'datetime',
                    'value' => $package->getUpdatedAt(),
                ],
                [
                    'label' => '',
                    'format' => 'raw',
                    'value' => Html::a('Open packagist.org', $package->getUrlRemote(), ['target' => '_blank', 'rel' => 'noopener noreferrer'])
                ]
            ]
        ]);?>
        <hr>

        <h3>Select version</h3>
        <div class="row">
            <div class="col-md-10">

                <?php if ($selectedVersion): ?>
                    <?= DetailView::widget([
                        'model' => $selectedVersion,
                        'attributes' => [
                            'version',
                            [
                                'attribute' => 'license',
                                'value' => isset($selectedVersion['license']) ? implode(', ', $selectedVersion['license']) : null
                            ],
                            [
                                'attribute' => 'authors',
                                'format' => 'raw',
                                'value' => isset($selectedVersion['authors']) ? implode(', ', \yii\helpers\ArrayHelper::getColumn($selectedVersion['authors'], 'name')) : null
                            ],
                            [
                                'attribute' => 'keywords',
                                'value' => isset($selectedVersion['keywords']) ? implode(', ', $selectedVersion['keywords']) : null
                            ],
                            [
                                'attribute' => 'type',
                                'value' => isset($selectedVersion['type']) ? $selectedVersion['type'] : null
                            ]
                        ]
                    ]);?>
                <?php endif ?>

                <?php if ($selectedVersionData):?>
                    <table class="table table-bordered">
                        <?php $selectedVersionDataSections = [
                            ['require', 'require-dev'],
                            ['provide', 'replace'],
                            ['suggest', 'conflict']
                        ] ?>

                        <?php foreach ($selectedVersionDataSections as $rows): ?>
                            <tr>
                            <?php foreach ($rows as $section): ?>
                                <td style="width: 50%;">
                                    <strong><?= Html::encode($section) ?></strong><br>
                                    <?= !empty($selectedVersionData[$section]) ? implode('<br>', $selectedVersionData[$section]) : Html::tag('small', '[empty]') ?>
                                </td>
                            <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </table>
                <?php endif ?>
            </div>

            <div class="col-md-2">
                <table class="table table-bordered">
                    <tr>
                        <th>Versions <span class="label label-info"><?= count($versions) ?></span></th>
                    </tr>
                    <?php $countVersion = 0 ?>
                    <?php foreach ($versions as $versionItem): ?>
                        <?php if (++$countVersion > 12): ?>
                            <?php break ?>
                        <?php endif ?>
                        <tr>
                            <td>
                                <?php if ($selectedVersion['version_normalized'] === $versionItem['version_normalized']): ?>
                                    <span><?= Html::encode($versionItem['version']) ?></span>
                                <?php else:?>
                                    <?= Html::a($versionItem['version'], Url::current(['version' => $versionItem['version']])) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            </div>
        </div>

        <?php if (!empty($readme)): ?>
            <hr>
            <?= \yii\apidoc\helpers\ApiMarkdown::process($readme) ?>
        <?php endif ?>
    <?php endif ?>
    <br>
</div>
