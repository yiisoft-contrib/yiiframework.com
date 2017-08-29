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
        <div class="btn-group btn-group-justified">
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
                $url['tag'] = $tag->name;
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
