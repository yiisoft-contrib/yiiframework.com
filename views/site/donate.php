<?php

use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var array $donationServices */

$this->title = 'Donate to Yii';
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container content donate-page style_external_links">
    <header class="donate-page__intro">
        <h1>Donate to Yii</h1>
        <p>Your support helps maintainers improve Yii and sustain its infrastructure.</p>
    </header>

    <section class="donate-options" aria-labelledby="donate-options-title">
        <div>
            <p class="donate-section__eyebrow">Support the project</p>
            <h2 id="donate-options-title">Choose a donation service</h2>
            <p>Use the service that is most convenient in your region.</p>
        </div>
        <div class="donate-options__actions">
            <?php foreach ($donationServices as $index => $donationService): ?>
                <a class="btn <?= $index === 0 ? 'btn-primary' : 'btn-default' ?>" href="<?= Html::encode($donationService['link']) ?>">
                    <?= Html::encode($donationService['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="donate-content">
        <section class="donate-section">
            <p class="donate-section__eyebrow">Impact</p>
            <h2>What donations support</h2>
            <ul class="donate-impact-list">
                <li>More development time for maintainers</li>
                <li>Servers and project infrastructure</li>
                <li>Development and design tools</li>
                <li>AI subscriptions</li>
                <li>Documentation and community outreach</li>
                <li>Long-term Yii 1.1 and Yii 2 maintenance</li>
            </ul>
        </section>

        <section class="donate-section">
            <p class="donate-section__eyebrow">Background</p>
            <h2>Why funding matters</h2>
            <p>Learn more about the project’s approach to sustainable development and community support.</p>
            <ul class="donate-reading-list">
                <li><?= Html::a('Preparing Yii for the long run', ['news/view', 'id' => 204]) ?></li>
            </ul>
        </section>

        <section class="donate-section donate-section--thanks">
            <p class="donate-section__eyebrow">Thank you</p>
            <h2>Every contribution helps</h2>
            <p>Thank you for helping Yii remain actively maintained, open source, and available to everyone.</p>
        </section>
    </div>
</main>
