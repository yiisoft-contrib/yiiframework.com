<?php
/**
 * @var Extension $model
 * @var int $revision
 */

use app\models\Extension;
use yii\helpers\Html;

$this->title = $model->name;

?>
<div class="container guide-view lang-en">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar', [
                'category' => $model->category_id,
            ]) ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <?= \app\widgets\Alert::widget() ?>

            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content extension-row">
                        <h2 class="title">
                            <?= Html::encode($model->name) ?>
                            <small>
                                <?= Html::encode($model->tagline) ?>
                                <?php if ($model->isTaglineAPreview()) {
                                    echo '('. Html::a('see full description', $model->packagist_url, [
                                        'target' => '_blank',
                                        'rel' => 'noopener noreferrer',
                                    ]) . ')';
                                } ?>
                            </small>
                        </h2>
                        <div class="text">
                            <?php $html = $model->contentHtml;
                            if ($model->from_packagist && $model->update_status == Extension::UPDATE_STATUS_NEW) {
                                $this->registerJs(<<<JS
                                    setTimeout(function () {
                                        location.reload();
                                    }, 3000);
JS
                                );
                                echo '<div class="packagist-spinner"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i><br>updating data from Packagist...</div>';
                            } else {
                                echo $html;
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-3">

                    <?= $this->render('_info.php', ['model' => $model]) ?>

                </div>
            </div>


        </div>
    </div>
</div>
<div class="comments-wrapper">
    <div class="container comments">
        <?= \app\widgets\Comments::widget([
            'objectType' => $model->getObjectType(),
            'objectId' => $model->getObjectId(),
            'prompt' => 'Please only use comments to help explain the above extension.<br/>If you have any questions, please ask in '.Html::a('the forum', Yii::$app->request->baseUrl . '/forum').' instead.',
        ]) ?>
    </div>
</div>
