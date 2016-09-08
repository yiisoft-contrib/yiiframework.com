<?php
/**
 * @var $this yii\web\View
 * @var $language string
 * @var $version string
 * @var $searchQuery string
 */
use app\widgets\DropdownList;
use app\models\Guide;
use yii\helpers\Html;

?>
<nav class="version-selector" role="navigation">
    <ul>
        <?= DropdownList::widget([
            'tag' => 'li',
            'selection' => $language ? $this->context->getLanguages()[$language] : 'Select Language',
            'items' => array_merge(
                $language ? [[
                    'label' => 'All',
                    'url' => ['/search/global', 'q' => $searchQuery, 'version' => $version],
                ]] : [],
                array_map(function ($language) use ($version, $searchQuery) {
                    $url = ['/search/global', 'q' => $searchQuery, 'language' => $language, 'version' => $version];
                    return [
                        'label' => $this->context->getLanguages()[$language],
                        'url' => $url,
                    ];
                }, array_keys($this->context->getLanguages()))
            ),
        ]) ?>
        <?= DropdownList::widget([
            'tag' => 'li',
            'selection' => $version ?: 'Select Version',
            'items' => array_merge($version ? [[
                    'label' => 'All',
                    'url' => ['/search/global', 'q' => $searchQuery, 'language' => $language],
                ]] : [],
                array_map(function ($version) use ($language, $searchQuery) {
                    $url = ['/search/global', 'q' => $searchQuery, 'language' => $language, 'version' => $version];
                    return [
                        'label' => $version,
                        'url' => $url,
                    ];
                }, $this->context->getVersions())
            ),
        ]) ?>
    </ul>
</nav>
