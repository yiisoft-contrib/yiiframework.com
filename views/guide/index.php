<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 */
use yii\helpers\Html;

$this->title = $guide->title;
$blocksPerRow = 4;
?>
<div class="guide-header-wrap">
    <div class="container guide-header lang-<?= $guide->language ?>" xmlns="http://www.w3.org/1999/xhtml">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="guide-headline"><?= Html::encode($guide->title) ?></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-offset-8 col-md-4">
                <?= $this->render('partials/_versions.php', ['guide' => $guide, 'section' => null]) ?>
            </div>
        </div>
    </div>
</div>
<div class="container guide-view lang-<?= $guide->language ?>">
    <div class="guide-content content">

        <div class="row">
    <?php
    $count = 0;
    foreach ($guide->chapters as $chapterTitle => $sections) {
        if ($count && $count % $blocksPerRow === 0) {
            echo '</div><div class="row">';
        }
    ?>
        <div class="col-sm-6 col-md-3">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Html::encode($chapterTitle) ?></h3>
                </div>
                <div class="panel-body">
                <?= Html::ul($sections, ['item' => function ($name, $title) use ($guide) {
                    return '<li>' . Html::a(Html::encode($title), ['guide/view',
                        'section' => $name,
                        'language' => $guide->language,
                        'version' => $guide->version,
                        'type' => $guide->typeUrlName
                    ]) . '</li>';
                }, 'class' => 'list-unstyled']) ?>
                </div>
            </div>
        </div>
    <?php
        $count++;
    }
    ?>
        </div>
</div>
</div>
