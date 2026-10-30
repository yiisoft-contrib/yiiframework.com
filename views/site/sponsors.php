<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $data */
/** @var array $groups */
/** @var string $currentFrom */

$this->title = 'Sponsors';
$this->params['breadcrumbs'][] = $this->title;
$snapshot = new DateTimeImmutable($data['snapshot']);
$from = new DateTimeImmutable($currentFrom);
?>
<main class="container sponsors-page">
    <header class="sponsors-page__intro">
        <h1>Thank you for supporting Yii</h1>
        <p>The people and organizations behind Yii’s continued development.</p>
        <?= Html::a('Support Yii', ['site/donate'], ['class' => 'btn btn-primary']) ?>
    </header>

    <?php foreach (['current' => 'Current sponsors', 'past' => 'Past sponsors'] as $key => $heading): ?>
        <section class="sponsors-section" aria-labelledby="sponsors-<?= $key ?>">
            <h2 id="sponsors-<?= $key ?>"><?= $heading ?></h2>
            <p class="sponsors-section__description">
                <?php if ($key === 'current'): ?>
                    Supporting Yii between <?= Html::encode($from->format('j F Y')) ?> and <?= Html::encode($snapshot->format('j F Y')) ?>.
                <?php else: ?>
                    With gratitude to those who contributed before <?= Html::encode($from->format('j F Y')) ?>.
                <?php endif; ?>
            </p>

            <ul class="sponsor-leaders">
                <?php foreach (array_slice($groups[$key], 0, $data['featuredCount']) as $sponsor): ?>
                    <li<?= isset($sponsor['logo']) ? '' : ' class="style_external_links"' ?>>
                        <a class="sponsor-leader<?= isset($sponsor['logo']) ? ' sponsor-leader--' . Html::encode($sponsor['logoStyle']) : ' sponsor-leader--text' ?>"
                           href="<?= Html::encode('https://opencollective.com/' . rawurlencode($sponsor['handle'])) ?>"
                           aria-label="<?= Html::encode($sponsor['name']) ?> on Open Collective">
                            <?php if (isset($sponsor['logo'])): ?>
                                <?= Html::img('@web/image/sponsors/' . $sponsor['logo'], [
                                    'alt' => $sponsor['logoStyle'] === 'wordmark' ? $sponsor['name'] : '',
                                    'class' => isset($sponsor['logoDark']) ? 'sponsor-logo sponsor-logo--light' : 'sponsor-logo',
                                ]) ?>
                                <?php if (isset($sponsor['logoDark'])): ?>
                                    <?= Html::img('@web/image/sponsors/' . $sponsor['logoDark'], [
                                        'alt' => '', 'class' => 'sponsor-logo sponsor-logo--dark', 'aria-hidden' => 'true',
                                    ]) ?>
                                <?php endif; ?>
                                <?php if ($sponsor['logoStyle'] === 'symbol'): ?>
                                    <span><?= Html::encode($sponsor['name']) ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <?= Html::encode($sponsor['name']) ?>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <ul class="sponsor-names style_external_links" aria-label="More <?= strtolower($heading) ?>">
                <?php foreach (array_slice($groups[$key], $data['featuredCount']) as $sponsor): ?>
                    <li><?= Html::a(Html::encode($sponsor['name']), 'https://opencollective.com/' . rawurlencode($sponsor['handle'])) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endforeach; ?>

    <section class="sponsors-thanks" aria-labelledby="sponsors-community">
        <h2 id="sponsors-community">Every contribution matters</h2>
        <p>Thank you also to our anonymous and guest contributors, and to the community supporting Yii
            through GitHub Sponsors, FundOSS and other pooled funds.</p>
        <p class="sponsors-page__basis style_external_links">
            Based on <a href="https://opencollective.com/yiisoft">Yii’s Open Collective</a> contribution records
            through <?= Html::encode($snapshot->format('j F Y')) ?>. Current means a contribution in the preceding
            <?= (int) $data['currentMonths'] ?> months; earlier contributors are listed as past sponsors.
            Leading supporters in each group are ordered by lifetime contributions, excluding reversed payments.
        </p>
    </section>
</main>
