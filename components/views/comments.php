<?php
/* @var $comments app\models\Comment[] */
/* @var $form yii\widgets\ActiveForm */
/* @var $commentForm app\models\Comment */
?>

<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="row">
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
                                    dlksajflkdsadjflksafjlfkadsjlfkj
                                </div>
                                <div class="comment-body">
                                    <div class="author" id="c<?= $comment->id ?>">
                                        <?php echo \cebe\gravatar\Gravatar::widget([
                                            'email' => $comment->user->email,
                                            'options' => [
                                                'alt' => $comment->user->username,
                                            ],
                                            'size' => 32
                                        ]); ?>
                                        <?= Html::encode($comment->user->username) ?>
                                    </div>
                                    <div class="text">
                                        <?= \yii\helpers\Markdown::process($comment->text) ?>
                                    </div>
                                    <div class="stuff">
                                        <a href="#c<?= $comment->id ?>">#<?= $comment->id ?></a>
                                        <span class="date"><?=Yii::$app->formatter->format($comment->created_at, 'datetime')?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>

            <?php if (!Yii::$app->user->isGuest): ?>
                <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($commentForm, 'text')->label('Add new comment')->textarea() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Comment', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            <?php else: ?>
                <p><?= Html::a('Signup', ['site/signup'])?> or <?= Html::a('Login', ['site/login']) ?> in order to comment.</p>
            <?php endif ?>
        </div>
    </div>
</div>
