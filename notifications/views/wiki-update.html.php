<?php

/** @var \yii\web\View $this */
/** @var \app\models\Wiki $wiki the wiki model object that has been changed */
/** @var \app\models\User $user the user object to whom the email is sent */
/** @var \app\models\WikiRevision $changes string, the changes */

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
