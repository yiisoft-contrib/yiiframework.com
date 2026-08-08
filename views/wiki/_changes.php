<?php

/** @var $left WikiRevision|DiffBehavior */
/** @var $right WikiRevision|DiffBehavior */

use app\models\WikiRevision;
use app\components\DiffBehavior;
?>
<?php foreach ([
    'title' => 'Title',
    'category.name' => 'Category',
    'yii_version' => 'Yii version',
    'tagNames' => 'Tags',
    'content' => 'Content',
] as $attribute => $label): ?>
    <?php $diff = $left->diff($right, $attribute); ?>
    <?php if (DiffBehavior::hasChanges($diff)): ?>
        <h4><?= $label ?></h4>
        <div class="diff">
            <?= trim(DiffBehavior::diffPrettyHtml($diff)) ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
