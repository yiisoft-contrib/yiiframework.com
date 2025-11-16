<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 */

use app\widgets\SearchForm;
use yii\helpers\Html;

$this->title = $guide->title;
$blocksPerRow = 4;

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions.php', ['guide' => $guide, 'section' => null]);
$this->endBlock();

?>
<div class="container guide-view">
    <div class="guide-content content lang-<?= $guide->language ?>">

        <?= SearchForm::widget([
            'type' => 'guide',
            'version' => $guide->version,
            'language' => $guide->language,
            'placeholder' => 'Search the Guide…',
        ]) ?>

        <div class="row">
    <?php
    $count = 0;
    foreach ($guide->chapters as $chapterTitle => $sections) {
        if ($count && $count % $blocksPerRow === 0) {
            echo '</div><div class="row">';
        }
    ?>
        <div class="col-sm-6 col-md-3">
            <div class="guide-panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Html::encode($chapterTitle) ?></h3>
                </div>
                <div class="panel-body">
                <?= Html::ul($sections, ['item' => static function ($name, $title) use ($guide) {
                    if (preg_match('~^https?://~', $name)) {
                        return '<li>' . Html::a(Html::encode($title), $name) . '</li>';
                    }
                    return '<li>' . Html::a(Html::encode($title), ['guide/view',
                        'section' => $name,
                        'language' => $guide->language,
                        'version' => $guide->version,
                        'type' => $guide->getTypeUrlName()
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
