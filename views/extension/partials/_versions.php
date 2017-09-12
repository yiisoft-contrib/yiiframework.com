<?php
/**
 * @var $this yii\web\View
 * @var $currentVersion string
 * @var $category string
 * @var $tag \app\models\ExtensionTag
 */
use app\widgets\DropdownList;

?>
<nav class="version-selector">
        <div class="btn-group btn-group-justified btn-group-1-element">
        <?php
        $versionItems = [];

        foreach (\app\models\Extension::getYiiVersionOptions() as $version => $label) {
            if ($version === $currentVersion) {
                continue;
            }

            $url = ['extension/index'];

            if ($version) {
                $url['version'] = $version;
            }

            if ($category) {
                $url['category'] = $category;
            }

            if ($tag) {
                $url['tag'] = $tag->slug;
            }

            $versionItems[] = [
                'label' => $label,
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => 'For Yii ' . $currentVersion,
            'items' => $versionItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
    </div>
</nav>
