<?php

use app\widgets\DropdownList;
use yii\helpers\Html;

/**
 * @var $this yii\web\View
 * @var $language string|null
 * @var $version string|null
 * @var $type string|null
 * @var $searchQuery string
 */

$hideVersion = $type === 'news';
$hideLanguage = $type === 'news' || in_array($type, ['wiki', 'extension', 'api'], true);

$typeItems = [['type' => null, 'label' => 'Whole site']];
foreach ($this->context->getTypes() as $itemType => $label) {
    $typeItems[] = ['type' => $itemType, 'label' => $label];
}
foreach ($typeItems as &$item) {
    $itemType = $item['type'];
    $url = ['/search/global', 'q' => $searchQuery, 'type' => $itemType];
    if ($itemType !== 'news') {
        $url['version'] = $version;
    }
    if (!in_array($itemType, ['news', 'wiki', 'extension', 'api'], true)) {
        $url['language'] = $language;
    }
    $item['url'] = $url;
}
unset($item);

$versionItems = [];
if (!$hideVersion) {
    $versionItems[] = [
        'version' => null,
        'label' => 'All',
        'url' => ['/search/global', 'q' => $searchQuery, 'language' => $language, 'type' => $type],
    ];

    $versions = array_values($this->context->getVersions());
    foreach ($versions as $itemVersion) {
        $label = $itemVersion === '3.0'
            ? 'Yii3'
            : ($itemVersion === '22.0' ? 'Yii 22' : ($itemVersion === '2.0' ? 'Yii 2' : "Yii $itemVersion"));
        $versionItems[] = [
            'version' => $itemVersion,
            'label' => $label,
            'url' => [
                '/search/global',
                'q' => $searchQuery,
                'language' => $language,
                'version' => $itemVersion,
                'type' => $type,
            ],
        ];
    }
}

$languageItems = [];
$languages = $this->context->getLanguages();
if (!$hideLanguage) {
    if ($language !== null) {
        $languageItems[] = [
            'label' => 'All languages',
            'url' => ['/search/global', 'q' => $searchQuery, 'version' => $version, 'type' => $type],
        ];
    }
    foreach ($languages as $itemLanguage => $languageName) {
        if ($itemLanguage === $language) {
            continue;
        }
        $languageItems[] = [
            'label' => $languageName,
            'url' => [
                '/search/global',
                'q' => $searchQuery,
                'language' => $itemLanguage,
                'version' => $version,
                'type' => $type,
            ],
        ];
    }
}

$languageIcon = '<svg viewBox="0 0 20 20" aria-hidden="true">'
    . '<path d="M10 1.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Zm5.99 7.5h-2.34a13.2 13.2 0 0 0-1.08-4.65 6.78 6.78 0 0 1 3.42 4.65ZM10 3.25c.77 0 1.87 2.22 2.14 6H7.86c.27-3.78 1.37-6 2.14-6Zm-2.57 1.36a13.2 13.2 0 0 0-1.08 4.64H4.01a6.78 6.78 0 0 1 3.42-4.64ZM4.01 10.75h2.34c.13 1.83.5 3.43 1.08 4.64a6.78 6.78 0 0 1-3.42-4.64ZM10 16.75c-.77 0-1.87-2.22-2.14-6h4.28c-.27 3.78-1.37 6-2.14 6Zm2.57-1.36a13.2 13.2 0 0 0 1.08-4.64h2.34a6.78 6.78 0 0 1-3.42 4.64Z"/>'
    . '</svg>';
?>
<nav class="version-selector search-selectors" aria-label="Search filters">
    <div class="search-selectors__group search-selectors__scope">
        <span class="version-selector__label">Search in</span>
        <div class="version-selector__options">
            <?php foreach ($typeItems as $item): ?>
                <?= Html::a($item['label'], $item['url'], [
                    'class' => 'version-selector__option' . ($item['type'] === $type ? ' is-active' : ''),
                    'aria-current' => $item['type'] === $type ? 'page' : null,
                ]) ?>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($versionItems): ?>
        <div class="search-selectors__group search-selectors__version">
            <span class="version-selector__label">Version</span>
            <div class="version-selector__options">
                <?php foreach ($versionItems as $item): ?>
                    <?= Html::a($item['label'], $item['url'], [
                        'class' => 'version-selector__option' . ($item['version'] === $version ? ' is-active' : ''),
                        'aria-current' => $item['version'] === $version ? 'page' : null,
                    ]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($languageItems): ?>
        <div class="search-selectors__language">
            <?= DropdownList::widget([
                'tag' => 'div',
                'selection' => $languageIcon . '<span>'
                    . Html::encode($language !== null ? $languages[$language] : 'All languages')
                    . '</span>',
                'items' => $languageItems,
                'options' => ['class' => 'dropdown context-dropdown search-context-dropdown'],
            ]) ?>
        </div>
    <?php endif; ?>
</nav>
