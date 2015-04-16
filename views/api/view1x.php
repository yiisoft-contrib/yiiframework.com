<?php
/**
 * @var $this yii\web\View
 * @var $versions array all available API versions
 * @var $version string the currently chosen API version
 * @var $section string the currently active API file
 * @var $content string the API page content
 * @var $packages array the API page menu structure
 */

use yii\apidoc\templates\bootstrap\SideNavWidget;
use yii\helpers\StringHelper;
use yii\helpers\Url;

$this->title = "Yii API Documentation $version";
?>

<div class="container api-content">
    <div class="row">
        <div class="col-md-3">
        <?php
        ksort($packages);
//        print_r($packages);
        $nav = [];
        foreach ($packages as $package => $classes) {
            $nav[$package] = [
                'label' => $package,
                'url' => '#',
                'items' => [],
            ];
            foreach($classes as $class) {
                $nav[$package]['items'][] = [
                    'label' => $class,
                    'url' => Url::to(['api/view', 'version' => $version, 'section' => $class]),
                    'active' => isset($section) && ($section == $class),
                ];
            }
        } ?>
        <?= SideNavWidget::widget([
            'id' => 'api-navigation',
            'items' => $nav,
            'view' => $this,
        ]) ?>
        </div>
        <div class="col-md-9" role="main">
            <?= $this->render('_versions.php', compact('version', 'versions', 'section')) ?>
            <?= $content ?>
        </div>
    </div>
</div>

<div class="container">
    <?= \app\components\Comments::widget([
        'objectType' => 'api',
        'objectId' => $version . '-' . $section,
    ]) ?>
</div>
