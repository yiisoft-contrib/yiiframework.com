<?php

/* @var $this \yii\web\View */
/* @var $wiki \app\models\Wiki the wiki model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */
/* @var $changes \app\models\WikiRevision string, the changes */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.html.php', [
    'model' => $wiki,
    'user' => $user,
    'title' => "Yii tutorial changed: {$wiki->title}",
    'css' => file_get_contents(__DIR__ . '/assets/diff.css'),
]) ?>
<p>
    The following tutorial that you are following was recently updated.
</p>
<hr />
<p>
    <b>Title:</b> <?= Html::a($wiki->title, Url::to($wiki->getUrl(), true)); ?><br/>
    <b>Updated:</b> <?= Yii::$app->formatter->asDatetime($changes->updated_at); ?><br/>
    <b>Summary:</b> <?= Html::encode($wiki->memo); ?><br/>
    <b>Changes:</b> <?php $url = Url::to($changes->getUrl(), true); echo Html::a($url, $url) ?>
</p>

<hr />
<?= $this->render('//wiki/_changes', [
    'left' => $changes->findPrevious(),
    'right' => $changes,
]) ?>
<hr />

<?php $this->endContent(); ?>
