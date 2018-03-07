<?php

use app\widgets\DropdownList;

/**
 * @var $this yii\web\View
 * @var $language string
 * @var $version string
 * @var $type string
 * @var $searchQuery string
 */

$columns = 3;
$hideVersion = false;
$hideLanguage = false;
if ($type === 'news') {
    $hideLanguage = true;
    $hideVersion = true;
    $columns = 1;
} elseif (in_array($type, ['wiki', 'extension', 'api'], true)) {
    $hideLanguage = true;
    $columns = 2;
}

?>
<nav class="version-selector">
    <div class="btn-group btn-group-justified btn-group-<?= $columns ?>-element">
        <?php
        $options = $this->context->getTypes();
        $types = array_keys($options);
        $typeItems = [];

        if ($type) {
            $typeItems[] = [
                'label' => 'Whole Site',
                'url' => ['/search/global', 'q' => $searchQuery, 'version' => $version, 'language' => $language],
            ];
        }

        foreach ($types as $t) {
            if ($t === $type) {
                continue;
            }

            $url = ['/search/global', 'q' => $searchQuery, 'language' => $language, 'version' => $version, 'type' => $t];
            $typeItems[] = [
                'label' => $options[$t],
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => $type ? $options[$type] : 'Whole Site',
            'items' => $typeItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
        <?php if (!$hideLanguage):
        $options = $this->context->getLanguages();
        $languages = array_keys($options);
        $languageItems = [];

        if ($language) {
            $languageItems[] = [
                'label' => 'All Languages',
                'url' => ['/search/global', 'q' => $searchQuery, 'version' => $version, 'type' => $type],
            ];
        }

        foreach ($languages as $lang) {
            if ($lang === $language) {
                continue;
            }

            $url = ['/search/global', 'q' => $searchQuery, 'language' => $lang, 'version' => $version, 'type' => $type];
            $languageItems[] = [
                'label' => $this->context->getLanguages()[$lang],
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => $language ? $this->context->getLanguages()[$language] : 'All Languages',
            'items' => $languageItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
        <?php endif;
        if (!$hideVersion):
        $versionItems = [];

        if ($version) {
            $versionItems[] = [
                'label' => 'All Versions',
                'url' => ['/search/global', 'q' => $searchQuery, 'language' => $language, 'type' => $type],
            ];
        }

        foreach ($this->context->getVersions() as $ver) {
            if ($version === $ver) {
                continue;
            }

            $url = ['/search/global', 'q' => $searchQuery, 'language' => $language, 'version' => $ver, 'type' => $type];
            $versionItems[] = [
                'label' => $ver,
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => $version ?: 'All Versions',
            'items' => $versionItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
        <?php endif; ?>
    </div>
</nav>
