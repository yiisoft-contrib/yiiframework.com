<?php
/**
 * @var yii\web\View $this
 * @var array $package
 * @var array $selectVersion
 * @var array $listVersion
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = $package['name'];
$this->params['breadcrumbs'][] = ['label' => 'Extension', 'url' => ['/extension']];
$this->params['breadcrumbs'][] = $this->title;

$selectVersionData = [];
if ($selectVersion) {
    foreach (['require', 'require-dev', 'suggest', 'provide', 'conflict', 'replace'] as $vName) {
        $selectVersionData[$vName] = [];

        if (!empty($selectVersion[$vName])) {
            foreach ($selectVersion[$vName] as $kVersionItem =>  $vVersionItem) {
                $versionItemName = Html::encode($kVersionItem);
                if (preg_match('/^([a-z\d\-_]+)\/([a-z\d\-_]+)$/i', $kVersionItem, $m)) {
                    $versionItemName = Html::a($versionItemName, [
                        'package',
                        'vendorName' => $m[1],
                        'packageName' => $m[2]
                    ]);
                }

                $selectVersionData[$vName][] = $versionItemName . ': ' . Html::encode($vVersionItem);
            }
        }
    }
}

?>

<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($package['name'])?></h1>
                <small><?= Html::encode($package['description']);?></small>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <br>
    <?= \app\widgets\Alert::widget();?>
    <? if ($package):?>
        <p><code class="hljs json language-json">composer require <?= Html::encode($package['name']);?></code></p>

        <?= DetailView::widget([
            'model' => $package,
            'attributes' => [
                'name',
                'description',
                [
                    'attribute' => 'repository',
                    'format' => 'raw',
                    'value' => Html::a(Html::encode($package['repositoryHost']), $package['repository'])
                ],
                [
                    'attribute' => 'downloads',
                    'format' => 'raw',
                    'value' => DetailView::widget([
                        'model' => $package['downloads'],
                        'attributes' => [
                            'total',
                            'monthly',
                            'daily'
                        ],
                    ])
                ],
                'favers',

                [
                    'attribute' => 'time',
                    'label' => 'Update time',
                    'format' => 'datetime'

                ],
                [
                    'label' => '',
                    'format' => 'raw',
                    'value' => Html::a('Open packagist.org', 'https://packagist.org/packages/' . $package['name'])
                ],
            ],
        ]);?>
        <hr>

        <h3>Select version</h3>
        <div class="row">
            <div class="col-md-10">
                <? if ($selectVersion):?>
                    <?= DetailView::widget([
                        'model' => $selectVersion,
                        'attributes' => [
                            'version',
                            [
                                'attribute' => 'license',
                                'value' => (isset($selectVersion['license']))? implode(', ', $selectVersion['license']): null
                            ],
                            [
                                'attribute' => 'authors',
                                'format' => 'raw',
                                'value' => (isset($selectVersion['authors']))?
                                    implode(', ', \yii\helpers\ArrayHelper::getColumn($selectVersion['authors'], 'name')): null
                            ],
                            [
                                'attribute' => 'keywords',
                                'value' => (isset($selectVersion['keywords']))? implode(', ', $selectVersion['keywords']): null
                            ],
                            [
                                'attribute' => 'type',
                                'value' => (isset($selectVersion['type']))? $selectVersion['type']: null
                            ],
                        ]
                    ]);?>
                <? endif;?>

                <? if ($selectVersionData):?>
                    <table class="table table-bordered">
                        <tr>
                            <td style="width: 50%;">
                                <strong>require</strong><br>
                                <?= implode('<br>', $selectVersionData['require']);?>
                            </td>
                            <td>
                                <strong>requires (dev)</strong><br>
                                <?= implode('<br>', $selectVersionData['require-dev']);?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>provide</strong><br>
                                <?= implode('<br>', $selectVersionData['provide']);?>
                            </td>
                            <td>
                                <strong>replaces</strong><br>
                                <?= implode('<br>', $selectVersionData['replace']);?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>suggests</strong><br>
                                <?= implode('<br>', $selectVersionData['suggest']);?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong>conflicts</strong><br>
                                <?= implode('<br>', $selectVersionData['conflict']);?>
                            </td>
                        </tr>
                    </table>
                <? endif;?>
            </div>

            <div class="col-md-2">
                <table class="table table-bordered">
                    <tr>
                        <th>Versions <span class="label label-info"><?= count($listVersion);?></span></th>
                    </tr>
                    <? foreach (array_slice($listVersion, 0, 10) as $versionItem):?>
                        <tr>
                            <td>
                                <? if ($selectVersion['version_normalized'] === $versionItem['version_normalized']):?>
                                    <span><?= Html::encode($versionItem['version']);?></span>
                                <? else:?>
                                    <?= Html::a(Html::encode($versionItem['version']), Url::current(['version' => $versionItem['version']]));?>
                                <? endif;?>
                            </td>
                        </tr>
                    <? endforeach;?>
                </table>
            </div>
        </div>

        <? if ($package['repoReadme']):?>
            <hr>
            <?= \yii\apidoc\helpers\ApiMarkdown::process($package['repoReadme']);?>
        <? endif;?>
    <? endif;?>
    <br>
</div>
