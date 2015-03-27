<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 */
use yii\helpers\Html;

$this->title = $guide->title;
$blocksPerRow = 4;
?>
<?= $this->render('_versions.php', ['guide' => $guide, 'section' => null]) ?>
<div class="guide-index content">


    <h1><?= Html::encode($guide->title) ?></h1>

    <?php
    $count = 0;
    foreach ($guide->chapters as $chapterTitle => $sections) {
        if ($count % $blocksPerRow === 0) {
            echo '<div class="row">';
        }
    ?>
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail">
                <h3><?= Html::encode($chapterTitle) ?></h3>
                <?= Html::ul($sections, ['item' => function ($name, $title) use ($guide) {
                    return '<li>' . Html::a(Html::encode($title), ['guide/view',
                        'section' => $name,
                        'language' => $guide->language,
                        'version' => $guide->version
                    ]) . '</li>';
                }, 'class' => 'list-unstyled']) ?>
            </div>
        </div>
    <?php
        if ($count++ % $blocksPerRow === $blocksPerRow - 1) {
            echo '</div>';
        }
    }
    if ($count && $count % $blocksPerRow !== $blocksPerRow - 1) {
        echo '</div>';
    }
    ?>
</div>
