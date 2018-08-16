<?php

use app\models\UserAvatarUploadForm;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $model \app\models\User */

$form = new UserAvatarUploadForm([
    'user' => $model,
]);

?>
<div class="profile-image-box">

<?php if ($model->hasAvatar()): ?>

    <?= Html::img($model->getAvatarUrl(), ['alt' => 'Your Avatar Image', 'class' => 'user-avatar-image']) ?>

<?php else: ?>

    <img class="user-avatar-image" alt="You currently don't have a profile picture.">
    <?php /*<div class="user-avatar-image">You currently don't have a profile picture.</div>*/ ?>

<?php endif; ?>

<div id="upload-progress" style="display: none;">
    <div class="bar" style="width: 0%;"></div>
</div>

<?= Html::beginForm(['user/upload-avatar'], 'post', ['enctype' => 'multipart/form-data']) ?>

    <?= Html::activeLabel($form, 'avatar') ?>
    <?= Html::activeFileInput($form, 'avatar', [
        'class' => 'fileupload-widget',
        'data' => [
            'url' => Url::to(['user/upload-avatar']),
            'upload-max-size' => $form->getMaxFileSize(),
        ],
    ]) ?>


    <noscript>
        <?= Html::submitButton('Upload', ['class' => 'btn btn-default']) ?>
    </noscript>

<?= Html::endForm(); ?>

<?php if ($model->hasAvatar()): ?>

    <?= Html::a('Remove Profile Picture', ['user/delete-avatar'], [
        'data' => [
            'confirm' => 'Are you sure you want to remove your profile picture?',
            'method' => 'post',
        ]
    ]) ?>

<?php endif; ?>


<?php /*\raoul2000\jcrop\JCropWidget::widget([
		'selector' => '#image_id',
		'pluginOptions' => [
			'aspectRatio' => 1,
			'minSize' => [50,50],
			'maxSize' => [200,200],
			'setSelect' => [10,10,40,40],
			'bgColor' => 'black',
			'bgOpacity' => '0.5',
			'onChange' => new yii\web\JsExpression('function(c){console.log(c.x);}')
		]
	]);*/ ?>

</div>