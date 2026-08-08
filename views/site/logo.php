<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Official Logos and Design';
$this->params['breadcrumbs'][] = $this->title;

$assetUrl = static function (string $path): string {
    return Yii::getAlias('@web/image/' . $path);
};

$yii3FullLogos = [
    ['name' => 'Black', 'description' => 'For light backgrounds', 'file' => 'yii3_full_black_for_light', 'canvas' => 'light'],
    ['name' => 'White', 'description' => 'For dark backgrounds', 'file' => 'yii3_full_white_for_dark', 'canvas' => 'dark'],
    ['name' => 'Color · light', 'description' => 'Full-color logo for light backgrounds', 'file' => 'yii3_full_for_light', 'canvas' => 'light'],
    ['name' => 'Color · dark', 'description' => 'Full-color logo for dark backgrounds', 'file' => 'yii3_full_for_dark', 'canvas' => 'dark'],
    ['name' => 'Grey · light', 'description' => 'Muted logo for light backgrounds', 'file' => 'yii3_full_grey_for_light', 'canvas' => 'light'],
    ['name' => 'Grey · dark', 'description' => 'Muted logo for dark backgrounds', 'file' => 'yii3_full_grey_for_dark', 'canvas' => 'dark'],
];

$yii3Signs = [
    ['name' => 'Color', 'file' => 'yii3_sign', 'canvas' => 'light'],
    ['name' => 'White', 'file' => 'yii3_sign_white', 'canvas' => 'dark'],
    ['name' => 'Black', 'file' => 'yii3_sign_black', 'canvas' => 'light'],
    ['name' => 'Grey', 'file' => 'yii3_sign_grey', 'canvas' => 'light'],
];
?>
<div class="container style_external_links">
    <main class="content logo-page">
        <header class="logo-page__intro">
            <h1>Yii logos and design</h1>
            <p>Official brand assets for websites, presentations, articles, and community projects.</p>
        </header>

        <nav class="logo-resources" aria-label="Yii 3 design resources">
            <div>
                <span class="logo-resources__label">Brand guidelines</span>
                <span class="logo-resources__description">Logo usage, colors, typography, and diagrams.</span>
            </div>
            <div class="logo-download-links">
                <?= Html::a('Figma', 'https://www.figma.com/file/UaFZwjYMre2KANBjVfEcys/yiiframework?node-id=0%3A1') ?>
                <?= Html::a('PDF', $assetUrl('design/guides/general.pdf')) ?>
                <?= Html::a('FIG', $assetUrl('design/guides/general.fig')) ?>
                <?= Html::a('Diagrams', $assetUrl('design/guides/diagrams.pdf')) ?>
            </div>
        </nav>

        <nav class="logo-resources" aria-label="Yii 3 favicon resources">
            <div>
                <span class="logo-resources__label">Favicon</span>
                <span class="logo-resources__description">Ready-to-use browser and application icons.</span>
            </div>
            <div class="logo-download-links">
                <?= Html::a('ICO', $assetUrl('design/favicon/favicon.ico')) ?>
                <?= Html::a('Full pack', $assetUrl('design/favicon/favicon.zip')) ?>
            </div>
        </nav>

        <section class="logo-collection">
            <div class="logo-collection__heading">
                <h2>Yii 3 full logos</h2>
                <p>Choose the variant that provides the best contrast with its background.</p>
            </div>
            <div class="logo-grid">
                <?php foreach ($yii3FullLogos as $logo): ?>
                    <article class="logo-card">
                        <div class="logo-card__preview logo-card__preview--<?= $logo['canvas'] ?>">
                            <img src="<?= $assetUrl('design/logo/' . $logo['file'] . '.svg') ?>"
                                 alt="<?= Html::encode($logo['name']) ?> Yii 3 logo">
                        </div>
                        <div class="logo-card__body">
                            <div>
                                <h3><?= Html::encode($logo['name']) ?></h3>
                                <p><?= Html::encode($logo['description']) ?></p>
                            </div>
                            <div class="logo-download-links">
                                <?= Html::a('SVG', $assetUrl('design/logo/' . $logo['file'] . '.svg')) ?>
                                <?= Html::a('PNG', $assetUrl('design/logo/' . $logo['file'] . '.png')) ?>
                                <?= Html::a('AI', $assetUrl('design/logo/' . $logo['file'] . '.ai')) ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="logo-collection">
            <div class="logo-collection__heading">
                <h2>Yii 3 sign</h2>
                <p>The compact symbol works well when horizontal space is limited.</p>
            </div>
            <div class="logo-grid logo-grid--signs">
                <?php foreach ($yii3Signs as $logo): ?>
                    <article class="logo-card">
                        <div class="logo-card__preview logo-card__preview--sign logo-card__preview--<?= $logo['canvas'] ?>">
                            <img src="<?= $assetUrl('design/logo/' . $logo['file'] . '.svg') ?>"
                                 alt="<?= Html::encode($logo['name']) ?> Yii 3 sign">
                        </div>
                        <div class="logo-card__body">
                            <h3><?= Html::encode($logo['name']) ?></h3>
                            <div class="logo-download-links">
                                <?= Html::a('SVG', $assetUrl('design/logo/' . $logo['file'] . '.svg')) ?>
                                <?= Html::a('PNG', $assetUrl('design/logo/' . $logo['file'] . '.png')) ?>
                                <?= Html::a('AI', $assetUrl('design/logo/' . $logo['file'] . '.ai')) ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="logo-collection">
            <div class="logo-collection__heading">
                <h2>Yii 2 logos</h2>
                <p>Original assets for projects and content related to Yii 2.</p>
            </div>
            <div class="logo-grid logo-grid--yii2">
                <?php foreach ([
                    ['name' => 'For light backgrounds', 'file' => 'yii_logo_light', 'canvas' => 'light'],
                    ['name' => 'For dark backgrounds', 'file' => 'yii_logo_dark', 'canvas' => 'dark'],
                ] as $logo): ?>
                    <article class="logo-card">
                        <div class="logo-card__preview logo-card__preview--<?= $logo['canvas'] ?>">
                            <img src="<?= $assetUrl($logo['file'] . '.svg') ?>" alt="Yii 2 logo <?= Html::encode(strtolower($logo['name'])) ?>">
                        </div>
                        <div class="logo-card__body">
                            <h3><?= Html::encode($logo['name']) ?></h3>
                            <div class="logo-download-links">
                                <?= Html::a('SVG', $assetUrl($logo['file'] . '.svg')) ?>
                                <?= Html::a('PNG', $assetUrl($logo['file'] . '.png')) ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <footer class="logo-license">
            <h2>License</h2>
            <p>The logos are licensed under the <?= Html::a(
                'Creative Commons Attribution-NoDerivatives 3.0 Unported License',
                'https://creativecommons.org/licenses/by-nd/3.0/'
            ) ?>.</p>
        </footer>
    </main>
</div>
