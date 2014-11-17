<?php

use app\apidoc\SideNavWidget;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $content string */
/* @var $chapters array */

?>
<div class="row">
    <div class="col-md-3">
        <?php
        $nav = [];
        foreach ($chapters as $chapter) {
            $items = [];
            foreach($chapter['content'] as $chContent) {
                $items[] = [
                    'label' => $chContent['headline'],
                    'url' => $this->context->generateGuideUrl($chContent['file']),
                    'active' => isset($currentFile) && ($chContent['file'] == basename($currentFile)),
                ];
            }
            $nav[] = [
                'label' => $chapter['headline'],
//                'url' => $this->context->generateGuideUrl($file),
                'items' => $items,
            ];
        } ?>
        <?= SideNavWidget::widget([
            'id' => 'navigation',
            'items' => $nav,
            'view' => $this,
        ]) ?>
    </div>
    <div class="col-md-9 guide-content" role="main">
        <div class="fixlink"><a href="<?= 'https://github.com/yiisoft/yii2/edit/master/docs/guide/' . basename($currentFile) ?>" class="h1" title="Found an error? Please help us fix it. Thanks!"><span class="glyphicon glyphicon-pencil"></a></div>
        <?= $content ?>
        <div class="toplink"><a href="#" class="h1" title="go to top"><span class="glyphicon glyphicon-arrow-up"></a></div>
    </div>
</div>
