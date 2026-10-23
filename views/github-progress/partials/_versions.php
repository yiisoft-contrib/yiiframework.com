<?php
/**
 * @var yii\web\View $this
 * @var string $currentVersion
 * @var string[] $versions
 */
use app\widgets\DropdownList;

?>
<nav class="version-selector">
    <div class="btn-group btn-group-justified btn-group-1-element">
        <?php
        $versionItems = [];

        foreach ($versions as $version) {

            $url = ['github-progress/index'];

            if ($version) {
                $url['version'] = $version;
            }

            $versionItems[] = [
                'label' => 'Version ' . $version,
                'url' => $url,
            ];
        }
        ?>
        <?= DropdownList::widget([
            'tag' => 'div',
            'selection' => 'Version ' . $currentVersion,
            'items' => $versionItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
    </div>
</nav>
