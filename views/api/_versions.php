<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 */
use app\components\DropdownList;
use yii\helpers\Html;

?>
<nav class="navbar navbar-default version-selector" role="navigation">
    <ul class="nav navbar-nav">
        <?= DropdownList::widget([
            'tag' => 'li',
            'selection' => "Version {$version}",
            'items' => array_map(function ($ver) use ($version, $section) {
                return [
                    'label' => $ver,
                    'url' => ['api/view', 'version' => $ver, 'section' => ($version[0] === $ver[0]) ? $section : 'index'],
                ];
            }, $versions),
        ]) ?>
    </ul>
</nav>
