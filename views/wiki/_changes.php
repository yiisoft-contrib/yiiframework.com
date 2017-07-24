<?php

/** @var $left WikiRevision|DiffBehavior */
/** @var $right WikiRevision|DiffBehavior */

use app\models\WikiRevision;
use app\components\DiffBehavior;
?>

<?php $diff = $left->diff($right, 'title'); ?>
<h4>
    Title
    <small><?php echo !DiffBehavior::hasChanges($diff) ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(DiffBehavior::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = $left->diff($right, 'category.name'); ?>
<h4>
    Category
    <small><?php echo !DiffBehavior::hasChanges($diff) ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(DiffBehavior::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = $left->diff($right, 'yii_version'); ?>
<h4>
    Yii version
    <small><?php echo !DiffBehavior::hasChanges($diff) ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(DiffBehavior::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = $left->diff($right, 'tagNames'); ?>
<h4>
    Tags
    <small><?php echo !DiffBehavior::hasChanges($diff) ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(DiffBehavior::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = $left->diff($right, 'content'); ?>
<h4>
    Content
    <small><?php echo !DiffBehavior::hasChanges($diff) ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(DiffBehavior::diffPrettyHtml($diff)) ?>
</div>
