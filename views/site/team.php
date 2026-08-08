<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $activeMembers array */
/* @var $pastMembers array */
/* @var $inactiveMembers array */

$this->title = 'Team';
$this->params['breadcrumbs'][] = $this->title;
?>

<main class="container content team-page">
    <header class="team-page__intro">
        <h1>Meet the team</h1>
        <p>Yii is developed and maintained by volunteers from around the world. Their experience and long-term care keep the framework moving forward.</p>
    </header>

    <section class="team-section" aria-labelledby="current-team-title">
        <header class="team-section__header">
            <div>
                <p class="team-eyebrow">Maintainers</p>
                <h2 id="current-team-title">Current team</h2>
            </div>
            <p>Active maintainers guide the project, develop the frameworks and extensions, and support Yii’s infrastructure and community.</p>
        </header>

        <?= $this->render('_teamMembers', [
            'memberRows' => $activeMembers,
            'modifier' => 'team-members--active',
        ]) ?>
    </section>

    <section class="team-section" aria-labelledby="inactive-team-title">
        <header class="team-section__header">
            <div>
                <p class="team-eyebrow">Extended team</p>
                <h2 id="inactive-team-title">Inactive members</h2>
            </div>
            <p>Team members who are not currently active but remain part of Yii’s story and expertise.</p>
        </header>

        <?= $this->render('_teamMembers', [
            'memberRows' => $inactiveMembers,
            'modifier' => 'team-members--inactive',
        ]) ?>
    </section>

    <section class="team-section" aria-labelledby="past-team-title">
        <header class="team-section__header">
            <div>
                <p class="team-eyebrow">With gratitude</p>
                <h2 id="past-team-title">Past members</h2>
            </div>
            <p>Former team members whose work helped shape Yii and the community around it.</p>
        </header>

        <?= $this->render('_teamMembers', [
            'memberRows' => $pastMembers,
            'modifier' => 'team-members--past',
        ]) ?>
    </section>

    <section class="team-contributors" aria-labelledby="contributors-title">
        <div>
            <p class="team-eyebrow">Community</p>
            <h2 id="contributors-title">Built by many</h2>
        </div>
        <div>
            <p>Yii grows through contributions to code, tests, documentation, translations, design, and reviews. Every focused contribution makes the framework better for everyone.</p>
            <?= Html::a('Learn how to contribute', ['site/contribute'], ['class' => 'btn btn-primary']) ?>
        </div>
    </section>
</main>
