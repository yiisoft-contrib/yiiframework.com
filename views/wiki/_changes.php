<?php

/** @var $left WikiRevision */
/** @var $right WikiRevision */

use app\models\WikiRevision;
?>

<?php $diff = WikiRevision::diff($left, $right, 'title'); ?>
<h4>
    Title
    <small><?php echo count($diff) <= 1 ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(WikiRevision::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = WikiRevision::diff($left, $right, 'category.name'); ?>
<h4>
    Category
    <small><?php echo count($diff) <= 1 ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(WikiRevision::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = WikiRevision::diff($left, $right, 'tagNames'); ?>
<h4>
    Tags
    <small><?php echo count($diff) <= 1 ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(WikiRevision::diffPrettyHtml($diff)) ?>
</div>

<?php $diff = WikiRevision::diff($left, $right, 'content'); ?>
<h4>
    Content
    <small><?php echo count($diff) <= 1 ? '<span class="label unchanged">unchanged</span>' : '<span class="label changed">changed</span>'; ?></small>
</h4>
<div class="diff">
    <?= trim(WikiRevision::diffPrettyHtml($diff)) ?>
</div>
