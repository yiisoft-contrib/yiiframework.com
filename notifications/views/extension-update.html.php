<?php

/* @var $this \yii\web\View */
/* @var $extension \app\models\Extension|DiffBehavior the extension model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */

use app\components\DiffBehavior;
use app\models\ExtensionCategory;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.html.php', [
    'model' => $extension,
    'user' => $user,
    'title' => "Yii extension updated: : {$extension->name}",
    'css' => file_get_contents(__DIR__ . '/assets/diff.css'),
]) ?>
<p>
    The following extension that you are following was recently updated.
</p>

<hr />
<p>
    <b>Name:</b> <?= Html::a($extension->name, Url::to($extension->getUrl(), true)); ?><br/>
    <b>Updated:</b> <?= Yii::$app->formatter->asDatetime($extension->updated_at); ?><br/>
    <b>Changes:</b>
</p>
<hr />
<?php

if ($extension->getReallyOldAttribute('category_id') != $extension->getAttribute('category_id')) {
    $diff = DiffBehavior::diffStrings(
        ExtensionCategory::findOne($extension->getReallyOldAttribute('category_id'))->name,
        $extension->category->name
    );
    echo "<h4>Category</h4>\n";
    echo '<div class="diff">' . DiffBehavior::diffPrettyHtml($diff) . '</div>';
}

$diff = $extension->diffAttribute('yii_version');
if (DiffBehavior::hasChanges($diff)) {
    echo "<h4>Yii Version</h4>\n";
    echo '<div class="diff">' . DiffBehavior::diffPrettyHtml($diff) . '</div>';
}

$diff = $extension->diffAttribute('license_id');
if (DiffBehavior::hasChanges($diff)) {
    echo "<h4>License</h4>\n";
    echo '<div class="diff">' . DiffBehavior::diffPrettyHtml($diff) . '</div>';
}

$diff = $extension->diffAttribute('github_url');
if (DiffBehavior::hasChanges($diff)) {
    echo "<h4>Github URL</h4>\n";
    echo '<div class="diff">' . DiffBehavior::diffPrettyHtml($diff) . '</div>';
}

$diff = $extension->diffAttribute('tagline');
if (DiffBehavior::hasChanges($diff)) {
    echo "<h4>Tagline</h4>\n";
    echo '<div class="diff">' . DiffBehavior::diffPrettyHtml($diff) . '</div>';
}

$diff = $extension->diffAttribute('description');
if (DiffBehavior::hasChanges($diff)) {
    echo "<h4>Description</h4>\n";
    echo '<div class="diff">' . DiffBehavior::diffPrettyHtml($diff) . '</div>';
}

?>
<hr />

<?php $this->endContent(); ?>
