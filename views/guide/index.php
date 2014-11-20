<?php
/**
 * @var $this yii\web\View
 * @var $model app\models\GuideSection
 */
use yii\helpers\Html;

$this->title = $model->getPageTitle();
$blocksPerRow = 4;
?>
<div class="guide-index">
    <h1><?= $model->getGuideTitle() ?></h1>

    <?php
    $count = 0;
    foreach ($model->getGuideChapters() as $chapter) {
        if ($count % $blocksPerRow === 0) {
            echo '<div class="row">';
        }
    ?>
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail">
                <h3><?= $chapter['headline'] ?></h3>
                <ul>
                    <?php foreach ($chapter['content'] as $chContent)
                        echo '<li>' . Html::a($chContent['headline'], ['guide/view', 'section' => $chContent['file'], 'language' => $model->language, 'version' => $model->version]) . '</li>';
                    ?>
                </ul>
            </div>
        </div>
    <?php
        if ($count++ % $blocksPerRow === 3) {
            echo '</div>';
        }
    }
    if ($count % $blocksPerRow !== 3) {
        echo '</div>';
    }
    ?>
</div>
