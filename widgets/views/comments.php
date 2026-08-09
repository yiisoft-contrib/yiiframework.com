<?php

/* @var $comments app\models\Comment[] */
/* @var $form yii\widgets\ActiveForm */
/* @var $commentForm app\models\Comment */

use app\components\UserPermissions;
use app\models\User;
use app\widgets\Voter;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="row user-notes__header" id="user-notes">
    <div class="col-md-offset-2 col-md-9">
        <?php if (!empty($comments)): ?>
            <h2 class="heading">User Contributed Notes <span class="badge"><?= count($comments) ?></span></h2>
        <?php else: ?>
            <h2 class="heading">User Contributed Notes</h2>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-offset-2 col-md-9">
        <div class="component-comments lang-en" id="comments">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="comment">
                                <div class="comment-header">
                                    <div class="row" id="c<?= $comment->id ?>">
                                        <div class="col-xs-6">
                                            <a href="#c<?= $comment->id ?>" class="comment-id">#<?= $comment->id ?></a>
                                        </div>
                                        <div class="col-xs-6 pull-right">
                                            <?= Voter::widget(['model' => $comment]) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-body">
                                    <div class="text">
                                        <?php
                                            echo Yii::$app->formatter->asCommentMarkdown($comment->text);
                                        ?>
                                    </div>
                                </div>
                                <div class="comment-footer">
                                    <?= $comment->user ? $comment->user->rankLink : User::DELETED_USER_HTML ?> at
                                    <span class="date text-muted"><small><?=Yii::$app->formatter->format($comment->created_at, 'datetime')?></small></span>
                                    <?php if (Yii::$app->user->can(UserPermissions::PERMISSION_MANAGE_COMMENTS)) {
                                        echo Html::a('View in comment admin', ['comment-admin/view', 'id' => $comment->id], ['class' => 'pull-right']);
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="row comment-composer">
    <div class="col-md-offset-2 col-md-9">
        <div class="comment-composer__header">
            <h3 class="heading">Leave a comment</h3>
            <?php if (isset($prompt)): ?>
                <div class="comment-composer__prompt"><?= $prompt ?></div>
            <?php endif; ?>
        </div>

        <?php if (!Yii::$app->user->isGuest): ?>
            <?php $form = ActiveForm::begin(['options' => ['class' => 'comment-composer__form']]); ?>
                <?= $form->field($commentForm, 'text')->label(false)->textarea([
                    'class' => 'markdown-editor',
                    'title' => 'Comment',
                    'placeholder' => 'Share a useful note, example, or clarification…',
                ]) ?>

                <div class="comment-composer__actions">
                    <span>Markdown is supported.</span>
                    <?= Html::submitButton('Post comment', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <div class="comment-composer__guest">
                <p>Join the conversation to share a note.</p>
                <div>
                    <?= Html::a('Log in', ['auth/login'], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Create an account', ['auth/signup'], ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>
