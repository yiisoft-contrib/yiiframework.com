<?php

use app\models\WikiRevision;
use yii\helpers\Html;

/** @var $model \app\models\Wiki */
/** @var $left WikiRevision */
/** @var $right WikiRevision */


$this->title = 'Wiki - ' . $model->title . ' - Compare Revisions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guide-header-wrap">
    <div class="container guide-header common-heading">
        <div class="row">
            <div class="col-md-12">
                <h1 class="guide-headline"><?= Html::encode($this->title) ?></h1>
            </div>
        </div>
    </div>
</div>


<div class="container guide-view lang-en" xmlns="http://www.w3.org/1999/xhtml">
    <div class="row">
        <div class="col-sm-3 col-md-2 col-lg-2">
            <?= $this->render('_sidebar') ?>
        </div>

        <div class="col-sm-9 col-md-10 col-lg-10" role="main">

            <!-- delete from here -->
            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="content wiki-row">
                        <h2 class="title">Difference between #<?= $left->revision; ?> and #<?= $right->revision; ?> of <?= Html::a(Html::encode($model->title), ['wiki/view', 'id' => $model->id, 'name' => $model->slug]) ?></h2>

                        <div class="revert">
                            <?php echo Html::a('Revert to #'.$left->revision, array('wiki/update','id'=>$model->id,'revision'=>$left->revision)); ?> |
                            <?php echo Html::a('Revert to #'.$right->revision, array('wiki/update','id'=>$model->id,'revision'=>$right->revision)); ?>
                        </div>


                        <h4>Title</h4>
                        <div class="entry">
                            <?php $diff = WikiRevision::diff($right, $left, 'title'); ?>
                            <?php echo empty($diff->getGroupedOpcodes()) ? '<div class="unchanged">unchanged</div>' : '<div class="changed">changed</div>'; ?>
                            <div class="label">Title</div>
                            <div class="diff">
                                <?php /*echo empty($diff->getGroupedOpcodes()) ? '<span>'.Html::encode($right->title).'</span>' : '<pre>'.trim(wordwrap($diff,80)).'</pre>';*/ ?>
                                <?= $diff->render(new \Diff_Renderer_Html_Inline)?>
                            </div>
                        </div>
                        <h4>Content</h4>
                        <div class="entry">
                            <?php $diff = WikiRevision::diff($right, $left, 'content'); ?>
                            <?php echo empty($diff->getGroupedOpcodes()) ? '<div class="unchanged">unchanged</div>' : '<div class="changed">changed</div>'; ?>
                            <div class="label">Title</div>
                            <div class="diff">
                                <?php /*echo empty($diff->getGroupedOpcodes()) ? '<span>'.Html::encode($right->title).'</span>' : '<pre>'.trim(wordwrap($diff,80)).'</pre>';*/ ?>
                                <?= $diff->render(new \Diff_Renderer_Html_Inline)?>
                            </div>
                        </div>
                        <?php /*
                        <div class="entry">
                            <?php $diff=TextDiff::compare((string)$right->category,(string)$left->category); ?>
                            <?php echo empty($diff) ? '<div class="unchanged">unchanged</div>' : '<div class="changed">changed</div>'; ?>
                            <div class="label">Category</div>
                            <div class="diff">
                                <?php echo empty($diff) ? '<span>'.Html::encode($right->category).'</span>' : '<pre>'.trim(wordwrap($diff,80)).'</pre>'; ?>
                            </div>
                        </div>
                        <div class="entry">
                            <?php $diff=TextDiff::compare($right->tags,$left->tags); ?>
                            <?php echo empty($diff) ? '<div class="unchanged">unchanged</div>' : '<div class="changed">changed</div>'; ?>
                            <div class="label">Tags</div>
                            <div class="diff">
                                <?php echo empty($diff) ? '<span>'.Html::encode($right->tags).'</span>' : '<pre>'.trim(wordwrap($diff,80)).'</pre>'; ?>
                            </div>
                        </div>
                        <div class="entry">
                            <?php $diff=TextDiff::compare($right->content,$left->content); ?>
                            <?php echo empty($diff) ? '<div class="unchanged">unchanged</div>' : '<div class="changed">changed</div>'; ?>
                            <div class="label">Content</div>
                            <div class="diff">
                                <pre><?php echo wordwrap(empty($diff) ? h($right->content) : $diff,80); ?></pre>
                            </div>
                        </div>
*/ ?>

                        <div class="text">
                            <?= $model->contentHtml ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-3">
                    <?= $this->render('_metadata.php', ['model' => $model]) ?>

                    <?= Html::a('Update Article', ['wiki/update', 'id' => $model->id])?>


                    <h3>Revisions</h3>

                    <?= $this->render('_revisions.php', ['model' => $model]) ?>
                </div>
            </div>


        </div>
    </div>
</div>
