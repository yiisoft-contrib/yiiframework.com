<?php

use app\components\UserPermissions;
use yii\helpers\Html;

/** @var $model app\models\Extension the data model */
/** @var $extended bool */

?>

<?= $this->render('_metadata.php', ['model' => $model, 'extended' => true]) ?>

<?php if ($model->hasApiDoc() || $model->hasGuideDoc()): ?>
    <?= $model->hasGuideDoc() ? Html::a('Guide Documentation', $model->getUrl('doc', ['type' => 'guide'])) . '<br>' : '' ?>
    <?= $model->hasApiDoc() ? Html::a('API Documentation', $model->getUrl('doc', ['type' => 'api'])) . '<br>' : '' ?>
    <hr>
<?php endif; ?>

<?php if (UserPermissions::canUpdateExtension($model)): ?>
    <?= Html::a('Update', ['extension/update', 'id' => $model->id])?><br>
    <?php if (UserPermissions::canManageExtensions()): ?>
        <?= Html::a('Delete', ['extension/delete', 'id' => $model->id], ['data-method' => 'POST'])?><br>
    <?php endif ?>
    <?php if (!$model->from_packagist): ?>
        <?= Html::a('Manage Downloads', ['extension/files', 'id' => $model->id])?><br>
    <?php endif; ?>
    <?php if ($model->from_packagist): ?>
        <?= Html::a('Update Packagist Data', ['extension/update-packagist', 'id' => $model->id], ['data-method' => 'post'])?><br>
    <?php endif; ?>
<?php endif; ?>

<?php if ($model->from_packagist): ?>
    <?= Html::a('Packagist Profile', $model->packagist_url, ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?><br>
<?php endif; ?>
<?php if ($model->github_url): ?>
    <?= Html::a(strpos($model->github_url, 'github.com/') !== false ? 'Github Repository' : 'Code Repository', $model->github_url, ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?><br>
<?php endif; ?>


<?php
$downloads = $model->getDownloads()->latest()->limit(3)->all();
if (!empty($downloads)): ?>
    <h3>Downloads</h3>

    <ul>
    <?php foreach($downloads as $download) {
        echo Html::tag('li', Html::a(Html::encode($download->file_name), $model->getUrl('download', ['filename' => $download->file_name])));
    } ?>
    </ul>

    <?= Html::a('show all', ['extension/files', 'id' => $model->id]) ?>


<?php endif; ?>

<?php $related = $model->getRelatedExtensions() ?>
<?php if (!empty($related)): ?>

<h3>Related Extensions</h3>

    <ul>
        <?php foreach($related as $extension) {
            echo "<li>" . Html::a(Html::encode($extension->getLinkTitle()), $extension->getUrl()) . '</li>';
        } ?>
    </ul>
<?php endif; ?>
