<?php

use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$isNotFound = $exception instanceof NotFoundHttpException;
$statusCode = property_exists($exception, 'statusCode') ? $exception->statusCode : null;
$this->title = $isNotFound ? 'Page not found' : $name;
?>

<main class="container content error-page">
    <div class="error-page__status" aria-hidden="true"><?= Html::encode($statusCode ?: '!') ?></div>
    <div class="error-page__content">
        <?php if ($isNotFound): ?>
            <h1>There’s nothing here.</h1>
            <p class="error-page__message">The page may have moved, or the address might be wrong.</p>
        <?php else: ?>
            <h1>We couldn’t complete your request.</h1>
            <p class="error-page__message"><?= nl2br(Html::encode($message)) ?></p>
            <p>Please try again, or let us know if the problem continues.</p>
        <?php endif; ?>

        <div class="error-page__actions">
            <?= Html::a('Homepage', ['site/index'], ['class' => 'error-page__action error-page__action--primary']) ?>
            <?= Html::a('Documentation', ['guide/index', 'version' => '3.0', 'language' => 'en'], ['class' => 'error-page__action']) ?>
            <?php if (!$isNotFound): ?>
                <?= Html::a('Contact us', ['site/contact'], ['class' => 'error-page__contact']) ?>
            <?php endif; ?>
        </div>
    </div>
</main>
