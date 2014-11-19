<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available guide versions
 * @var $version string the currently chosen guide version
 * @var $languages array all available languages (language ID => language name)
 * @var $language string the currently chosen guide language ID
 * @var $title string the page title
 * @var $section string the section name
 * @var $content string the section content
 */
use yii\helpers\Html;

$this->title = $title;
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
                            <?= Html::a($ver, ['guide/view', 'version' => $ver, 'language' => $language, 'section' => $section], [
                                'role' => 'menuitem',
                                'tabindex' => -1,
                            ]) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= $languages[$language] ?> <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach ($languages as $lang => $name): ?>
                        <li role="presentation">
                            <?= Html::a($name, ['guide/view', 'version' => $version, 'language' => $lang, 'section' => $section], [
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
