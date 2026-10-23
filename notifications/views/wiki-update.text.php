<?php

/** @var \yii\web\View $this */
/** @var \app\models\Wiki $wiki the wiki model object that has been changed */
/** @var \app\models\User $user the user object to whom the email is sent */
/** @var \app\models\WikiRevision $changes string, the changes */

use app\components\DiffBehavior;
use app\models\WikiCategory;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.text.php', [
    'model' => $wiki,
    'user' => $user,
]); ?>
The following tutorial that you are following was recently updated.

TITLE:   <?= $wiki->title; ?>

URL:     <?= Url::to($wiki->getUrl(), true); ?>

UPDATED: <?= Yii::$app->formatter->asDatetime($changes->updated_at); ?>

SUMMARY: <?= $changes->memo; ?>

CHANGES: <?= Url::to($changes->getUrl(), true); ?>

-------------------------------------------------------------------------------
<?php

/** @var \app\models\WikiRevision|DiffBehavior $left */
/** @var \app\models\WikiRevision|DiffBehavior $right */
$left = $changes->findPrevious();
$right = $changes;

$diff = $left->diff($right, 'title');
if (DiffBehavior::hasChanges($diff)) {
    echo 'Old Category: ' . $left->title . "\n";
    echo 'New Category: ' . $right->title . "\n\n";
}

$diff = $left->diff($right, 'category_id');
if (DiffBehavior::hasChanges($diff)) {
    $oldCategory = WikiCategory::findOne($left->category_id);
    $newCategory = WikiCategory::findOne($right->category_id);
    echo 'Old Category: ' . ($oldCategory ? $oldCategory->name : '') . "\n";
    echo 'New Category: ' . ($newCategory ? $newCategory->name : '') . "\n\n";
}

$diff = $left->diff($right, 'yii_version');
if (DiffBehavior::hasChanges($diff)) {
    echo 'Old Yii Version: ' . $left->yii_version . "\n";
    echo 'New Yii Version: ' . $right->yii_version . "\n\n";
}

$diff = $left->diff($right, 'tagNames');
if (DiffBehavior::hasChanges($diff)) {
    echo 'Old Tags: ' . $left->tagNames . "\n";
    echo 'New Tags: ' . $right->tagNames . "\n\n";
}

$diff = $left->diff($right, 'content');
if (DiffBehavior::hasChanges($diff)) {
    echo "Content:\n";
    echo DiffBehavior::diffPrettyText($diff);
    echo "\n";
}

?>
-------------------------------------------------------------------------------
<?php $this->endContent(); ?>
