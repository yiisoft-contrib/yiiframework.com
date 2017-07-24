<?php

/* @var $this \yii\web\View */
/* @var $wiki \app\models\Wiki the extension model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */
/* @var $changes \app\models\WikiRevision string, the changes */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.html.php', [
    'model' => $wiki,
    'user' => $user,
    'title' => "Yii tutorial changed: {$wiki->title}",
    'css' => <<<CSS
        diff, .memo {
            font-family: monospace;
            font-weight: normal;

            color: #000;
            background-color: #fcfcfc;
            border-left: 5px solid #EBF1F2;
            overflow: auto;
            word-wrap: normal;

            padding: 15px;
        }

        .diff .diff-snip {
            color: #b2dbbf;
            font-style: italic;
            margin: 2.5rem 0;
        }

        .diff ins {
            background: rgba(133, 215, 201, 0.71);
            text-decoration: none;
        }

        .diff del {
            background: rgba(255, 22, 84, 0.6);
            text-decoration: line-through #f00;
        }
        .changed {
            font-weight: normal;
            background: rgba(255, 22, 84, 0.6);
            color: #000;
        }
        .unchanged {
            font-weight: normal;
            background: rgba(133, 215, 201, 0.71);
            color: #000;
        }
CSS
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
