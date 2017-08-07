<?php
/**
 * @var $this yii\web\View
 * @var $language string
 * @var $version string
 * @var $searchQuery string
 */
use app\widgets\DropdownList;

?>
<nav class="version-selector">
    <div class="btn-group btn-group-justified">
        <?php
        $options = $this->context->getLanguages();
        $languages = array_keys($options);
        $languageItems = [];

        if ($language) {
            $languageItems[] = [
                'label' => 'All Languages',
                'url' => ['/search/global', 'q' => $searchQuery, 'version' => $version],
            ];
        }

        foreach ($languages as $lang) {
            if ($lang === $language) {
                continue;
            }

            $url = ['/search/global', 'q' => $searchQuery, 'language' => $lang, 'version' => $version];



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
        <?php
        $versionItems = [];

        if ($version) {
            $versionItems[] = [
                'label' => 'All Versions',
                'url' => ['/search/global', 'q' => $searchQuery, 'language' => $language],
            ];
        }

        foreach ($this->context->getVersions() as $ver) {
            if ($version === $ver) {
                continue;
            }

            $url = ['/search/global', 'q' => $searchQuery, 'language' => $language, 'version' => $ver];

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
    </div>
</nav>
