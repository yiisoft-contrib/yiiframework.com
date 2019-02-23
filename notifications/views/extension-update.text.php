<?php

/* @var $this \yii\web\View */
/* @var $extension \app\models\Extension|DiffBehavior the extension model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */

use app\components\DiffBehavior;
use app\models\ExtensionCategory;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.text.php', [
    'model' => $extension,
    'user' => $user,
]); ?>
The following extension that you are following was recently updated.

NAME:    <?= $extension->name; ?>

URL:     <?= Url::to($extension->getUrl(), true); ?>

UPDATED: <?= Yii::$app->formatter->asDatetime($extension->updated_at); ?>

CHANGES:
-------------------------------------------------------------------------------
<?php

if ($extension->getReallyOldAttribute('category_id') != $extension->getAttribute('category_id')) {
    $oldCategory = ExtensionCategory::findOne($extension->getReallyOldAttribute('category_id'));
    echo 'Old Category: ' . ($oldCategory ? $oldCategory->name : '') . "\n";
    echo 'New Category: ' . ($extension->category ? $extension->category->name : '') . "\n\n";
}

if ($extension->getReallyOldAttribute('yii_version') != $extension->getAttribute('yii_version')) {
    echo 'Old Yii Version: ' . $extension->getReallyOldAttribute('yii_version') . "\n";
    echo 'New Yii Version: ' . $extension->yii_version . "\n\n";
}

if ($extension->getReallyOldAttribute('license_id') != $extension->getAttribute('license_id')) {
    echo 'Old License: ' . $extension->getReallyOldAttribute('license_id') . "\n";
    echo 'New License: ' . $extension->license_id . "\n\n";
}

if ($extension->getReallyOldAttribute('github_url') != $extension->getAttribute('github_url')) {
    echo 'Old Github URL: ' . $extension->getReallyOldAttribute('github_url') . "\n";
    echo 'New Github URL: ' . $extension->github_url . "\n\n";
}

if ($extension->getReallyOldAttribute('tagline') != $extension->getAttribute('tagline')) {
    echo 'Old Tagline: ' . $extension->getReallyOldAttribute('tagline') . "\n";
    echo 'New Tagline: ' . $extension->tagline . "\n\n";
}

$diff = $extension->diffAttribute('description');
if (DiffBehavior::hasChanges($diff)) {
    echo "Description:\n";
    echo DiffBehavior::diffPrettyText($diff);
    echo "\n";
}

?>
-------------------------------------------------------------------------------
<?php $this->endContent(); ?>
