<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var $missingTranslation bool
 * @var $type string
 * @var Doc $doc
 */

use app\models\Doc;
use yii\helpers\Html;

$this->beginContent('@app/views/guide/partials/_guide-layout.php', [
    'guide' => $guide,
    'section' => $section,
    'type' => $type,
    'missingTranslation' => !empty($missingTranslation),
]);

?>
<div class="guide-content content lang-<?= (!empty($missingTranslation)) ? 'en' : $guide->language ?>">

    <?php if (!empty($missingTranslation)): ?>
        <div class="alert alert-warning lang-en">
            <strong>This section is not translated yet.</strong> <br />
            Please read it in English and consider
            <a href="https://github.com/yiisoft/yii2/blob/master/docs/internals/translation-workflow.md#documentation">
            helping us with translation</a>.
        </div>
    <?php endif ?>
    <div class="pull-right">
        <?= \app\widgets\Star::widget(['model' => $doc]) ?>
    </div>

    <?= $section->content ?>

    <div class="prev-next">
        <?php
        if (($prev = $section->getPrevSection()) !== null) {
            $left = '<span class="chevron-left" aria-hidden="true"></span> ';
            echo '<div class="prev-next__left">' . Html::a($left . Html::encode($prev[1]), ['guide/view', 'section' => $prev[0], 'version' => $guide->version, 'language' => $guide->language, 'type' => $guide->typeUrlName]) . '</div>';
        }
        echo '<div class="prev-next__center"><a href="#top">Go to Top <span class="chevron-up" aria-hidden="true"></span></a></div>';
        if (($next = $section->getNextSection()) !== null) {
            $right = ' <span class="chevron-right" aria-hidden="true"></span>';
            echo '<div class="prev-next__right">' . Html::a(Html::encode($next[1]) . $right, ['guide/view', 'section' => $next[0], 'version' => $guide->version, 'language' => $guide->language, 'type' => $guide->typeUrlName]) . '</div>';
        }
        ?>
    </div>

    <?php if (($editUrl = $section->editUrl) !== false): ?>
    <div class="edit-box">
        <div class="edit-icon"><i class="fa fa-github"></i></div>
        <p class="lang-en">
            Found a typo or you think this page needs improvement?<br />
                <a href="<?= $editUrl; ?>">Edit it on github</a> !
        </p>
    </div>
    <?php endif; ?>

</div>
<?php $this->endContent() ?>

<?php if ($doc): ?>
    <div class="comments-wrapper">
        <div class="container comments">
            <?= \app\widgets\Comments::widget([
                'objectType' => $doc->getObjectType(),
                'objectId' => $doc->getObjectId(),
            ]) ?>
        </div>
    </div>
<?php endif ?>
