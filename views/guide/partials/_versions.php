<?php

/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection|null
 * @var $extensionName string
 * @var $extensionVendor string
 */

use app\models\Guide;
use app\widgets\DropdownList;
use yii\helpers\Html;

$downloadItems = [];
if ($guide->type === Guide::TYPE_GUIDE) {
    foreach (
        [
            'pdf' => ['PDF', 'Portable document'],
            'tar.gz' => ['Offline HTML', 'Gzip archive'],
            'tar.bz2' => ['Offline HTML', 'Bzip2 archive'],
        ] as $format => [$label, $description]
    ) {
        if ($guide->getDownloadFile($format) !== false) {
            $downloadItems[] = [
                'label' => '<strong>' . $label . '</strong><small>' . $description . ' · ' . $format . '</small>',
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

$languageItems = [];
foreach ($guide->getLanguageOptions() as $language => $languageName) {
    if ($guide->language === $language) {
        continue;
    }

    if (isset($extensionName)) {
        if (isset($section)) {
            if (!$section->hasTranslation($language)) {
                continue;
            }
            $url = [
                'guide/extension-view',
                'section' => $section->name,
                'version' => $guide->version,
                'language' => $language,
                'vendorName' => $extensionVendor,
                'name' => $extensionName,
            ];
        } else {
            $url = [
                'guide/extension-index',
                'version' => $guide->version,
                'language' => $language,
                'vendorName' => $extensionVendor,
                'name' => $extensionName,
            ];
        }
    } elseif (isset($section)) {
        if (!$section->hasTranslation($language)) {
            continue;
        }
        $url = [
            'guide/view',
            'section' => $section->name,
            'version' => $guide->version,
            'language' => $language,
            'type' => $guide->getTypeUrlName(),
        ];
    } else {
        $url = [
            'guide/index',
            'version' => $guide->version,
            'language' => $language,
            'type' => $guide->getTypeUrlName(),
        ];
    }

    $languageItems[] = ['label' => $languageName, 'url' => $url];
}

$versions = array_values($guide->getVersionOptions());
usort($versions, static fn(string $a, string $b): int => version_compare($b, $a));
$versionItems = [];

foreach ($versions as $version) {
    $language = $guide->language;

    if ($version !== $guide->version) {
        $otherGuide = isset($extensionName)
            ? Guide::loadExtension($guide->extension, $version, $language)
            : Guide::load($version, $language, $guide->type);

        if ($otherGuide === null) {
            $language = Guide::LANGUAGE_EN;
            $otherGuide = isset($extensionName)
                ? Guide::loadExtension($guide->extension, $version, $language)
                : Guide::load($version, $language, $guide->type);
        }
    } else {
        $otherGuide = $guide;
    }

    $keepSection = isset($section)
        && $otherGuide !== null
        && $guide->version[0] === $version[0]
        && $otherGuide->loadSection($section->name) !== null;

    if (isset($extensionName)) {
        $url = $keepSection
            ? [
                'guide/extension-view',
                'section' => $section->name,
                'version' => $version,
                'language' => $language,
                'vendorName' => $extensionVendor,
                'name' => $extensionName,
            ]
            : [
                'guide/extension-index',
                'version' => $version,
                'language' => $language,
                'vendorName' => $extensionVendor,
                'name' => $extensionName,
            ];
    } else {
        $url = $keepSection
            ? [
                'guide/view',
                'section' => $section->name,
                'version' => $version,
                'language' => $language,
                'type' => $guide->getTypeUrlName(),
            ]
            : [
                'guide/index',
                'version' => $version,
                'language' => $language,
                'type' => $guide->getTypeUrlName(),
            ];
    }

    $versionItems[] = ['version' => $version, 'url' => $url];
}

if ($guide->type === Guide::TYPE_GUIDE && !isset($extensionName)) {
    array_unshift($versionItems, [
        'version' => '3.0',
        'url' => 'https://yiisoft.github.io/docs/guide/',
        'external' => true,
    ]);
}

$languageIcon = '<svg viewBox="0 0 20 20" aria-hidden="true">'
    . '<path d="M10 1.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Zm5.99 7.5h-2.34a13.2 13.2 0 0 0-1.08-4.65 6.78 6.78 0 0 1 3.42 4.65ZM10 3.25c.77 0 1.87 2.22 2.14 6H7.86c.27-3.78 1.37-6 2.14-6Zm-2.57 1.36a13.2 13.2 0 0 0-1.08 4.64H4.01a6.78 6.78 0 0 1 3.42-4.64ZM4.01 10.75h2.34c.13 1.83.5 3.43 1.08 4.64a6.78 6.78 0 0 1-3.42-4.64ZM10 16.75c-.77 0-1.87-2.22-2.14-6h4.28c-.27 3.78-1.37 6-2.14 6Zm2.57-1.36a13.2 13.2 0 0 0 1.08-4.64h2.34a6.78 6.78 0 0 1-3.42 4.64Z"/>'
    . '</svg>';
$downloadIcon = '<svg viewBox="0 0 20 20" aria-hidden="true">'
    . '<path d="M9.25 2h1.5v8.45l2.8-2.8 1.06 1.06L10 13.31 5.39 8.7l1.06-1.06 2.8 2.8V2ZM3 14h1.5v2h11v-2H17v3.5H3V14Z"/>'
    . '</svg>';
?>
<nav class="version-selector guide-selector" aria-label="Guide options">
    <span class="version-selector__label">Version</span>
    <div class="version-selector__options">
        <?php foreach ($versionItems as $item): ?>
            <?php
            $version = $item['version'];
            $label = $version === '3.0' ? 'Yii 3' : ($version === '2.0' ? 'Yii 2' : "Yii $version");
            $linkOptions = [
                'class' => 'version-selector__option' . ($version === $guide->version ? ' is-active' : ''),
                'aria-current' => $version === $guide->version ? 'page' : null,
            ];
            if (!empty($item['external'])) {
                $linkOptions['target'] = '_blank';
                $linkOptions['rel'] = 'noopener noreferrer';
            }
            ?>
            <?= Html::a($label, $item['url'], $linkOptions) ?>
        <?php endforeach; ?>
    </div>

    <div class="guide-selector__actions">
        <?php if ($languageItems): ?>
            <?= DropdownList::widget([
                'tag' => 'div',
                'selection' => $languageIcon . '<span>' . Html::encode($guide->getLanguageName()) . '</span>',
                'items' => $languageItems,
                'options' => ['class' => 'dropdown guide-context-dropdown guide-language-dropdown'],
            ]) ?>
        <?php endif; ?>

        <?php if ($downloadItems): ?>
            <?= DropdownList::widget([
                'tag' => 'div',
                'selection' => $downloadIcon . '<span>Downloads</span>',
                'items' => $downloadItems,
                'encodeLabels' => false,
                'options' => ['class' => 'dropdown guide-context-dropdown guide-download-dropdown'],
            ]) ?>
        <?php endif; ?>
    </div>
</nav>
