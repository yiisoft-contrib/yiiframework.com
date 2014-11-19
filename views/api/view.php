<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $content string the API page content
 */
use yii\helpers\Html;

$this->title = "Yii API Documentation $version";
?>
<nav class="navbar navbar-default guide-view" role="navigation">
    <div class="container">
        <form class="nav navbar-form navbar-right" role="search">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Search">
            </div>
        </form>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Version <?= $version ?> <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($versions as $ver): ?>
                        <li role="presentation">
                            <?= Html::a($ver, ['api/view', 'version' => $ver, 'section' => $section], [
                                'role' => 'menuitem',
                                'tabindex' => -1,
                            ]) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<?= $content ?>
