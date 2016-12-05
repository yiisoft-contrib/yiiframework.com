<?php
/* @var $comments app\models\Comment[] */
/* @var $form yii\widgets\ActiveForm */
/* @var $commentForm app\models\Comment */
?>

<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="row" id="user-notes">
    <div class="col-md-offset-2 col-md-9">
        <?php if (!empty($comments)): ?>
            <span class="heading">User Contributed Notes <span class="badge"><?= count($comments) ?></span></span>
        <?php else: ?>
            <span class="heading">User Contributed Notes</span>
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
                                        <div class="col-md-1">
                                            <a href="#c<?= $comment->id ?>" class="comment-id">#<?= $comment->id ?></a>
                                        </div>
                                        <div class="col-md-9 details">
                                            <a href="#"><?= $comment->user->username; ?></a> at
                                            <span class="date"><?=Yii::$app->formatter->format($comment->created_at, 'datetime')?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <?= \app\widgets\Voter::widget(['model' => $comment]) ?>
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
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-offset-2 col-md-9">
        <span class="heading">Leave a comment</span>
    </div>
</div>
<?php if (isset($prompt)): ?>
<div class="row">
    <div class="col-md-offset-2 col-md-9">
        <?= $prompt ?>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-offset-2 col-md-9">
        <?php if (!Yii::$app->user->isGuest): ?>
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($commentForm, 'text')->label(false)->textarea(['class' => 'markdown-editor']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Comment', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <p><?= Html::a('Signup', ['auth/signup'])?> or <?= Html::a('Login', ['auth/login']) ?> in order to comment.</p>
        <?php endif ?>
    </div>
</div>
