<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $memberRows array */
/* @var $modifier string */

$githubIcon = '<svg viewBox="0 0 20 20" aria-hidden="true"><path fill="currentColor" d="M10 .25a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.46-1.11-1.46-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.89 1.53 2.34 1.09 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.09.39-1.98 1.03-2.68-.11-.25-.45-1.27.09-2.65 0 0 .84-.27 2.75 1.03A9.6 9.6 0 0 1 10 5.08c.85.01 1.7.12 2.5.34 1.9-1.29 2.74-1.02 2.74-1.02.54 1.38.2 2.39.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.69-4.56 4.93.35.3.67.91.67 1.85v2.74c0 .26.18.58.69.48A10 10 0 0 0 10 .25Z"/></svg>';
$xIcon = '<svg viewBox="0 0 20 20" aria-hidden="true"><path fill="currentColor" d="M3.1 2h3.8l3.8 5.1L15.2 2h1.7l-5.4 6.3L17.4 18h-3.8l-4.3-5.8L4.2 18H2.5l6-7L3.1 2Zm3 1.4 8.2 13.2h1.1L7.2 3.4H6.1Z"/></svg>';
?>

<div class="team-members <?= Html::encode($modifier) ?>">
    <?php foreach ($memberRows as $row): ?>
        <?php foreach ($row as $member): ?>
            <article class="team-member">
                <div class="team-member__portrait">
                    <img src="<?= Html::encode(Yii::getAlias($member['photo'] ?? '@web/image/team/noimage.png')) ?>" alt="<?= Html::encode($member['name']) ?>" loading="lazy">
                </div>
                <div class="team-member__body">
                    <div class="team-member__heading">
                        <h3><?= Html::encode($member['name']) ?></h3>
                        <?php if (isset($member['github']) || isset($member['x'])): ?>
                            <div class="team-member__socials">
                                <?php if (isset($member['github'])): ?>
                                    <?= Html::a($githubIcon, 'https://github.com/' . $member['github'], [
                                        'aria-label' => $member['name'] . ' on GitHub',
                                        'title' => $member['name'] . ' on GitHub',
                                    ]) ?>
                                <?php endif; ?>
                                <?php if (isset($member['x'])): ?>
                                    <?= Html::a($xIcon, 'https://x.com/' . $member['x'], [
                                        'aria-label' => $member['name'] . ' on X',
                                        'title' => $member['name'] . ' on X',
                                    ]) ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="team-member__role"><?= HtmlPurifier::process($member['duty']) ?></p>
                    <p class="team-member__period"><?= Html::encode($member['period']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
