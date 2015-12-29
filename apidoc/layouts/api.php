<?php

use app\apidoc\ApiRenderer;
use app\components\SideNav;
use yii\apidoc\templates\bootstrap\SideNavWidget;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $types array */
/* @var $content string */

/** @var $renderer ApiRenderer */
$renderer = $this->context;
?>
<div class="row row-offcanvas">
    <div class="col-sm-2 col-md-2 col-lg-2">
        <?php
        $types = $renderer->getNavTypes(isset($type) ? $type : null, $types);
        ksort($types);
        $nav = [];
        foreach ($types as $i => $class) {
            $namespace = $class->namespace;
            if (empty($namespace)) {
                $namespaceLabel = 'Not namespaced classes';
            } else {
                // apply level classes to all namespace levels except the last one
                $namespaceLabel = [];
                foreach(explode('\\', $namespace) as $level => $ns) {
                    if ($level < substr_count($namespace, '\\')) {
                        $namespaceLabel[] = Html::tag('span', Html::encode($ns) . '\\', ['class' => "api-ns-level-$level"]);
                    } else {
                        $namespaceLabel[] = Html::encode($ns);
                    }
                }
                $namespaceLabel = implode('', $namespaceLabel);
            }
            if (!isset($nav[$namespace])) {
                $nav[$namespace] = [
                    'label' => $namespaceLabel,
                    'encodeLabel' => false,
                    'url' => '#',
                    'items' => [],
                ];
            }
            $nav[$namespace]['items'][] = [
                'label' => StringHelper::basename($class->name),
                'url' => $renderer->generateApiUrl($class->name),
                'active' => isset($type) && ($class->name == $type->name),
            ];
        } ?>
        <?= SideNav::widget([
            'id' => 'api-navigation',
            'items' => $nav,
            'view' => $this,
            'options' => ['class' => 'sidenav-offcanvas'],
        ])?>
    </div>
    <div class="col-sm-10 col-md-10 col-lg-10" role="main">
        <!-- YII_VERSION_SELECTOR -->
        <div class="content">
        <?= $content ?>
        </div>
    </div>
</div>
