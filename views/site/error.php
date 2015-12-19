<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="row">
        <div class="site-error content">

            <div class="alert alert-warning">
                <?= nl2br(Html::encode($message)) ?>
            </div>
            <img src="<?= Yii::getAlias('@web/image/404.jpg') ?>"
                alt="Something is wrong: <?= Html::encode($this->title) ?>"
                class="img-responsive" />
            <p>
                The above error occurred while the Web server was processing your request.
            </p>
            <p>
                Please <?= Html::a('contact us', Url::to(['site/contact'])) ?> if you think this is a server error. Thank you.
            </p>

        </div>
    </div>
</div>
