<?php
/**
 * @var $this yii\web\View
 * @var $alternatives array
 * @var $alternativeVersions array
 */

use app\widgets\SearchForm;
use yii\helpers\Html;

$this->title = 'Page not found';
?>
<main class="container content docs-error docs-error--api">
    <div class="docs-error__hero">
        <div class="docs-error__status" aria-hidden="true">404</div>
        <div class="docs-error__intro">
            <h1>Not in this API.</h1>
            <p>The class or package may have moved, or it may be documented in another version.</p>

            <?php if (!isset($extension)): // TODO search currently does not work for extensions ?>
                <div class="docs-error__search">
                    <?= SearchForm::widget([
                        'type' => 'api',
                        'placeholder' => 'Search the API…',
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="docs-error__recovery-grid">

            <?php if (!empty($alternatives)): ?>
                <section class="docs-error__recovery">
                <h2>Available elsewhere</h2>
                <p>This item exists in another API version.</p>
                <ul class="docs-error__options">
                <?php foreach($alternatives as $version => $url): ?>
                    <li><?= Html::a("Version $version", $url) ?></li>
                <?php endforeach; ?>
                </ul>
                </section>
            <?php endif; ?>

            <section class="docs-error__recovery">
            <h2>Choose another API</h2>
            <p>Browse the available API documentation<?= isset($extension) ? ' for ' . Html::encode($extension->name) : '' ?>.</p>
            <ul class="docs-error__options">
                <?php foreach ($alternativeVersions as $version => $url): ?>
                    <li><?= Html::a("Version $version", $url) ?></li>
                <?php endforeach; ?>
            </ul>
            </section>
    </div>
</main>
