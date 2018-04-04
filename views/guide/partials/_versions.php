<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var $extensionName string
 * @var $extensionVendor string
 */
use app\widgets\DropdownList;
use app\models\Guide;

?>
<nav class="version-selector">
        <div class="btn-group btn-group-justified btn-group-3-element">
        <?php if ($guide->type === 'guide') {
            $items = [];
            if ($guide->getDownloadFile('pdf') !== false) {
                $items[] = [
                    'label' => 'PDF',
                    'url' => ['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'pdf'],
                ];
            }
            if ($guide->getDownloadFile('tar.gz') !== false) {
                $items[] = [
                    'label' => 'Offline HTML (tar.gz)',
                    'url' => ['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'tar.gz'],
                ];
            }
            if ($guide->getDownloadFile('tar.bz2') !== false) {
                $items[] = [
                    'label' => 'Offline HTML (tar.bz2)',
                    'url' => ['guide/download', 'version' => $guide->version, 'language' => $guide->language, 'format' => 'tar.bz2'],
                ];
            }
            if (!empty($items)) {
                echo DropdownList::widget([
                    'tag' => 'div',
                    'selection' => 'Download',
                    'items' => $items,
                    'options' => [
                        'class' => 'btn-group btn-group-sm'
                    ]
                ]);
            }
        } ?>
        <?php
        $options = $guide->getLanguageOptions();
        $languages = array_keys($options);
        $languageItems = [];

        foreach ($languages as $language) {
            if ($guide->language === $language) {
                continue;
            }

            if (isset($extensionName)) {
                if (isset($section)) {
                    $url = ['guide/extension-view', 'section' => $section->name, 'version' => $guide->version, 'language' => $language, 'vendorName' => $extensionVendor, 'name' => $extensionName];
                } else {
                    $url = ['guide/extension-index', 'version' => $guide->version, 'language' => $language, 'vendorName' => $extensionVendor, 'name' => $extensionName];
                }
            } else {
                if (isset($section)) {
                    $url = ['guide/view', 'section' => $section->name, 'version' => $guide->version, 'language' => $language, 'type' => $guide->typeUrlName];
                } else {
                    $url = ['guide/index', 'version' => $guide->version, 'language' => $language, 'type' => $guide->typeUrlName];
                }
            }

            $languageItems[] = [
                'label' => $options[$language],
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => $guide->getLanguageName(),
            'items' => $languageItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
                ]
        ]) ?>

        <?php
        $versionItems = [];
        $language = $guide->language;

        foreach ($guide->getVersionOptions() as $version) {
            if ($version === $guide->version) {
                continue;
            }

            if (isset($extensionName)) {
                $otherGuide = Guide::loadExtension($guide->extension, $version, $language);
                if ($otherGuide === null) {
                    $language = 'en';
                    $otherGuide = Guide::loadExtension($guide->extension, $version, $language);
                }
                if (isset($section) && $guide->version[0] === $version[0] && $otherGuide->loadSection($section->name) !== null) {
                    $url = ['guide/extension-view', 'section' => $section->name, 'version' => $version, 'language' => $language, 'vendorName' => $extensionVendor, 'name' => $extensionName];
                } else {
                    $url = ['guide/extension-index', 'version' => $version, 'language' => $language, 'vendorName' => $extensionVendor, 'name' => $extensionName];
                }
            } else {
                $otherGuide = Guide::load($version, $language, $guide->type);
                if ($otherGuide === null) {
                    $language = 'en';
                    $otherGuide = Guide::load($version, $language, $guide->type);
                }
                if (isset($section) && $guide->version[0] === $version[0] && $otherGuide->loadSection($section->name) !== null) {
                    $url = ['guide/view', 'section' => $section->name, 'version' => $version, 'language' => $language, 'type' => $guide->typeUrlName];
                } else {
                    $url = ['guide/index', 'version' => $version, 'language' => $language, 'type' => $guide->typeUrlName];
                }
            }
            $versionItems[] = [
                'label' => $version,
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => "Version {$guide->version}",
            'items' => $versionItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
    </div>
</nav>
