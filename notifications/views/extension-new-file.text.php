<?php

/* @var $this \yii\web\View */
/* @var $extension \app\models\Extension|DiffBehavior the extension model object that has been changed */
/* @var $user \app\models\User the user object to whom the email is sent */
/* @var $file \app\models\File the file object that has been uploaded */

use app\components\DiffBehavior;
use yii\helpers\Url;

?>
<?php $this->beginContent('@app/notifications/views/_layout.text.php', [
    'model' => $extension,
    'user' => $user,
]); ?>
The following extension that you are following was recently updated.

NAME:    <?= $extension->name; ?>

URL:     <?= Url::to($extension->getUrl(), true); ?>


A new file has been uploaded:

    <?= $file->file_name ?> (<?= Yii::$app->formatter->asShortSize($file->file_size) ?>)
    <?= Url::to($extension->getUrl('download', ['filename' => $file->file_name]), true) ?>

    <?= $file->summary ?>

<?php $this->endContent(); ?>
