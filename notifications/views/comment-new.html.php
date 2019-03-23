<?php
/**
 * @var Linkable|ObjectIdentityInterface $model the model object that just received a new comment
 * @var $comment \app\models\Comment the comment object
 * @var $user \app\models\User the user object to whom the email is sent
 */

use app\components\object\ObjectIdentityInterface;
use app\models\Linkable;
use yii\helpers\Url;
use yii\helpers\Html;

?>
<?php $this->beginContent('@app/notifications/views/_layout.html.php', [
	'model' => $model,
	'user' => $user,
	'title' => 'A new comment was added: ' . $model->getLinkTitle(),
]) ?>
	<p>A new comment was added to <?= Html::encode($model->getObjectType()) ?> you are following:</p>
	<p><?= Html::a($model->getLinkTitle(), Url::to($model->getUrl(), true)) ?></p>
	<hr />
	<p>
		<b>By:</b> <?= Html::a(Html::encode($comment->user->display_name), $comment->user->getUrl()); ?>
			at <?= Yii::$app->formatter->asDatetime($comment->created_at); ?>
	</p>
	<div style="padding:0.5em;margin:1em 0;background:#eee;">
		<?php echo Yii::$app->formatter->asCommentMarkdown($comment->text); ?>
	</div>

	<hr />

<?php $this->endContent(); ?>
