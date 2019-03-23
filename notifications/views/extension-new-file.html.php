<?php

/* @var $this \yii\web\View */
/* @var $extension \app\models\Extension|DiffBehavior the extension model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */
/* @var $file \app\models\File the file object that has been uploaded */

use app\components\DiffBehavior;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.html.php', [
    'model' => $extension,
    'user' => $user,
    'title' => "Yii extension updated: : {$extension->name}",
]) ?>
<p>
    The following extension that you are following was recently updated.
</p>

<hr />
<p>
    <b>Name:</b> <?= Html::a($extension->name, Url::to($extension->getUrl(), true)); ?><br/>
</p>
<p>
    A new file has been uploaded:
</p>
<p>
    <?= Html::a(Html::encode($file->file_name), Url::to($extension->getUrl('download', ['filename' => $file->file_name]), true)) ?>
    (<?= Yii::$app->formatter->asShortSize($file->file_size) ?>)<br>
    <em><?= Html::encode($file->summary) ?></em>
</p>

<hr />

<?php $this->endContent(); ?>
