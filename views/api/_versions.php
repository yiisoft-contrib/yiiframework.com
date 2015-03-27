<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 */
use yii\helpers\Html;

?>
<nav class="navbar navbar-default version-selector" role="navigation">
    <ul class="nav navbar-nav">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Version <?= $version ?> <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <?php foreach ($versions as $ver): ?>
                    <li role="presentation">
                        <?= Html::a($ver, ['api/view', 'version' => $ver, 'section' => ($version[0] === $ver[0]) ? $section : 'index'], [
                            'role' => 'menuitem',
                            'tabindex' => -1,
                        ]) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>
</nav>
