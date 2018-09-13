<?php
/**
 * @var $this yii\web\View
 * @var $currentVersion string
 * @var $category \app\models\WikiCategory
 * @var $tag \app\models\WikiTag
 */
use app\widgets\DropdownList;

?>
<nav class="version-selector">
        <div class="btn-group btn-group-justified btn-group-1-element">
        <?php
        $versionItems = [];

        foreach (\app\models\Wiki::getYiiVersionOptions() as $version => $label) {
            if ($version === $currentVersion) {
                continue;
            }

            $url = ['wiki/index'];

            if ($version) {
                $url['version'] = $version;
            }

            if ($category) {
                $url['category'] = $category->id;
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
            'selection' => \app\models\Wiki::getYiiVersionOptions()[$currentVersion] ?? null,
            'items' => $versionItems,
            'options' => [
                'class' => 'btn-group btn-group-sm'
            ]
        ]) ?>
    </div>
</nav>
