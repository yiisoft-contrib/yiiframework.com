<?php
/**
 * @var $model \app\models\Linkable the model object that just received a new comment
 * @var $comment \app\models\Comment the comment object
 * @var $user \app\models\User the user object to whom the email is sent
 */
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.text.php', [
    'model' => $model,
    'user' => $user,
]); ?>
A new comment was added to the following content you are following:

[<?= $model->getItemType() ?>] <?= $model->getLinkTitle(); ?>

<?= Url::to($model->getUrl(), true); ?>


-------------------------------------------------------------------------------
BY: <?= $comment->user->display_name; ?> at <?= Yii::$app->formatter->asDatetime($comment->created_at); ?>


<?= wordwrap(strip_tags(Yii::$app->formatter->asCommentMarkdown($comment->text)), 80); ?>

-------------------------------------------------------------------------------
<?php $this->endContent(); ?>
