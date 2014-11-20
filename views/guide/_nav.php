<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\GuideSection
 */
use app\apidoc\SideNavWidget;
use yii\helpers\Url;

$nav = [];
foreach ($model->getGuideChapters() as $chapter) {
    $items = [];
    foreach($chapter['content'] as $chContent) {
        $items[] = [
            'label' => $chContent['headline'],
            'url' => Url::to(['guide/view', 'section' => $chContent['file'], 'language' => $model->language, 'version' => $model->version]),
            'active' => $model->name === $chContent['file'],
        ];
    }
    $nav[] = [
        'label' => $chapter['headline'],
        'items' => $items,
    ];
}

echo SideNavWidget::widget([
    'id' => 'navigation',
    'items' => $nav,
    'view' => $this,
]);
