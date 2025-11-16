<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $extensionName string
 * @var $extensionVendor string
 */
use yii\helpers\Html;

$this->title = $guide->title;
$blocksPerRow = 4;

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions.php', ['guide' => $guide, 'section' => null, 'extensionName' => $extensionName, 'extensionVendor' => $extensionVendor]);
$this->endBlock();

?>
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
            <div class="guide-panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Html::encode($chapterTitle) ?></h3>
                </div>
                <div class="panel-body">
                <?= Html::ul($sections, ['item' => static function ($name, $title) use ($guide, $extensionVendor, $extensionName) {
                    if (preg_match('~^https?://~', $name)) {
                        return '<li>' . Html::a(Html::encode($title), $name) . '</li>';
                    }
                    return '<li>' . Html::a(Html::encode($title), ['guide/extension-view',
                        'section' => $name,
                        'vendorName' => $extensionVendor,
                        'name' => $extensionName,
                        'language' => $guide->language,
                        'version' => $guide->version,
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
