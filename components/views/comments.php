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
                                    <div class="row" id="c<?= $comment->id ?>">
                                        <div class="col-md-1">
                                            <a href="#c<?= $comment->id ?>" class="comment-id">#<?= $comment->id ?></a>
                                        </div>
                                        <div class="col-md-9 details">
                                            <a href="#"><?= $comment->user->username; ?></a> at
                                            <span class="date"><?=Yii::$app->formatter->format($comment->created_at, 'datetime')?></span>
                                        </div>
                                        <div class="col-md-1 voting votes-up">
                                            <span class="votes">0</span> <i class="fa fa-thumbs-up"></i>
                                        </div>
                                        <div class="col-md-1 voting votes-down">
                                            <span class="votes">0</span> <i class="fa fa-thumbs-down"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="comment-body">
                                    <div class="text">
                                        <?php
                                            $pre_processed_text = \yii\helpers\Markdown::process($comment->text);
                                            $highlighter = new \Highlight\Highlighter();
                                            $highlighter->setAutodetectLanguages(array("php", "javascript", "html"));
                                            $matches = array();
                                            $pattern = "/<code>(.*?)<\\/code>/is";
                                            preg_match_all($pattern, $pre_processed_text, $matches);
                                            if(count($matches) > 0) {
                                                foreach($matches[1] as $match){
                                                    $processed = $highlighter->highlightAuto(html_entity_decode($match));
                                                    $pre_processed_text = str_replace($match, $processed->value, $pre_processed_text);
                                                    $pre_processed_text = str_replace('<code>', '<pre><code class="hljs '.$processed->language.'">', $pre_processed_text);
                                                    $pre_processed_text = str_replace('</code>', '</code></pre>', $pre_processed_text);
                                                }
                                            }
                                            echo $pre_processed_text;
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
<div class="row">
    <div class="col-md-offset-2 col-md-9">
        <?php if (!Yii::$app->user->isGuest): ?>
            <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($commentForm, 'text')->label('')->textarea() ?>

                <div class="form-group">
                    <?= Html::submitButton('Comment', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <p><?= Html::a('Signup', ['site/signup'])?> or <?= Html::a('Login', ['site/login']) ?> in order to comment.</p>
        <?php endif ?>
    </div>
</div>
