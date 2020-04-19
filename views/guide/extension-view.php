<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var $extensionName string
 * @var $extensionVendor string
 */

use app\models\Doc;
use app\widgets\SideNav;
use yii\helpers\Html;

$this->beginContent('@app/views/guide/partials/_guide-layout.php', [
    'guide' => $guide,
    'section' => $section,
    'extensionName' => $extensionName,
    'extensionVendor' => $extensionVendor,
]);
?>
<div class="guide-content content lang-<?= (!empty($missingTranslation)) ? 'en' : $guide->language ?>">
    <?php if (!empty($missingTranslation)): ?>
        <div class="alert alert-warning lang-en">
            <strong>This section is not translated yet.</strong> <br />
            Please read it in English and consider
            <a href="https://github.com/yiisoft/yii2/blob/master/docs/internals/translation-workflow.md">
            helping us with translation</a>.
        </div>
    <?php endif ?>

    <?= $section->content ?>

    <div class="prev-next">
        <?php
        if (($prev = $section->getPrevSection()) !== null) {
            $left = '<span class="chevron-left" aria-hidden="true"></span> ';
            echo '<div class="prev-next__left">' . Html::a($left . Html::encode($prev[1]), ['guide/extension-view', 'section' => $prev[0], 'version' => $guide->version, 'language' => $guide->language, 'name' => $extensionName, 'vendorName' => $extensionVendor]) . '</div>';
        }
        echo '<div class="prev-next__center"><a href="#top">Go to Top <span class="chevron-up" aria-hidden="true"></span></a></div>';
        if (($next = $section->getNextSection()) !== null) {
            $right = ' <span class="chevron-right" aria-hidden="true"></span>';
            echo '<div class="prev-next__right">' . Html::a(Html::encode($next[1]) . $right, ['guide/extension-view', 'section' => $next[0], 'version' => $guide->version, 'language' => $guide->language, 'name' => $extensionName, 'vendorName' => $extensionVendor]) . '</div>';
        }
        ?>
    </div>

    <!-- TODO add edit URL -->

</div>
<?php $this->endContent() ?>
