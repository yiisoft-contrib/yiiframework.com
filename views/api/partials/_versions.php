<?php

/**
 * @var $this yii\web\View
 * @var string[] $versions all available API versions
 * @var string $version the currently chosen API version
 * @var string $section the currently active API file
 * @var app\models\Extension|null $extension
 */

use app\models\Guide;
use app\widgets\DropdownList;
use yii\helpers\Html;

$versions = array_values($versions);
usort($versions, static fn(string $a, string $b): int => version_compare($b, $a));

$downloadItems = [];
$guide = Guide::load($version, 'en');
if (!isset($extension) && $guide) {
    foreach (['tar.gz', 'tar.bz2'] as $format) {
        if ($guide->getDownloadFile($format) !== false) {
            $downloadItems[] = [
                'label' => "Offline HTML ($format)",
                'url' => [
                    'guide/download',
                    'version' => $guide->version,
                    'language' => $guide->language,
                    'format' => $format,
                ],
            ];
        }
    }
}
?>
<nav class="version-selector" aria-label="Yii version">
    <span class="version-selector__label">Version</span>
    <div class="version-selector__options">
        <?php foreach ($versions as $ver): ?>
            <?php
            $url = null;
            if (isset($extension)) {
                [$extensionVendor, $extensionName] = explode('/', $extension->name, 2);
                $sectionFile = Yii::getAlias("@app/data/extensions/{$extension->name}/api-$ver/$section.html");
                if ($ver !== $version && !is_file($sectionFile)) {
                    continue;
                }
                $url = $section === 'index'
                    ? ['api/extension-index', 'version' => $ver, 'vendorName' => $extensionVendor, 'name' => $extensionName]
                    : ['api/extension-view', 'version' => $ver, 'vendorName' => $extensionVendor, 'name' => $extensionName, 'section' => $section];
            } else {
                $url = ($version[0] !== $ver[0] || $section === 'index')
                    ? ['api/index', 'version' => $ver]
                    : ['api/view', 'version' => $ver, 'section' => $section];
            }
            $label = $ver === '3.0' ? 'Yii 3' : ($ver === '2.0' ? 'Yii 2' : "Yii $ver");
            ?>
            <?= Html::a($label, $url, [
                'class' => 'version-selector__option' . ($ver === $version ? ' is-active' : ''),
                'aria-current' => $ver === $version ? 'page' : null,
            ]) ?>
        <?php endforeach; ?>
    </div>

    <?php if ($downloadItems): ?>
        <div class="version-selector__download">
            <?= DropdownList::widget([
                'tag' => 'div',
                'selection' => 'Download',
                'items' => $downloadItems,
                'options' => ['class' => 'btn-group btn-group-sm'],
            ]) ?>
        </div>
    <?php endif; ?>
</nav>
