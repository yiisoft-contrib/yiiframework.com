<?php

/**
 * This file renders the header navigation for the Yii website and also for the Yii Forum based on Discourse.
 *
 * IMPORTANT NOTE: If you change this file, make sure changes are reflected in the Discourse header also!
 *
 * If this file is rendered for Discourse, the $discourse variable is set to `true`.
 */

use app\widgets\InfoTop;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\web\View;

/* @var $this View */
/* @var $discourse boolean */

if ($discourse) {
    $controller = 'forum';
    $action = 'index';
} else {
    $controller = Yii::$app->controller->id ?? null;
    $action = Yii::$app->controller && Yii::$app->controller->action ? Yii::$app->controller->action->id : null;
}

?>
<?= InfoTop::widget() ?>
<header class="navbar navbar-inverse navbar-static<?= !$discourse && !Yii::$app->user->isGuest ? ' navbar--authenticated' : '' ?>" id="top">
    <div class="container">
        <div id="main-nav-head" class="navbar-header">
            <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                <img class="yii-logo yii-logo--light"
                     src="<?= Yii::getAlias('@web/image/design/logo/yii3_full_for_light.svg') ?>"
                     alt="Yii Framework" width="185" height="40"/>
                <img class="yii-logo yii-logo--dark"
                     src="<?= Yii::getAlias('@web/image/design/logo/yii3_full_for_dark.svg') ?>"
                     alt="" width="185" height="40" aria-hidden="true"/>
            </a>
            <?php if (!$discourse): ?>
                <a href="https://github.com/yiisoft" class="header-github" target="_blank" rel="noopener noreferrer" aria-label="Yii on GitHub, 34 thousand stars">
                    <svg viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10 .248c-5.525 0-10 4.477-10 10a9.998 9.998 0 0 0 6.838 9.487c.5.094.683-.215.683-.48 0-.238-.008-.867-.013-1.7-2.781.602-3.368-1.343-3.368-1.343-.455-1.154-1.112-1.462-1.112-1.462-.906-.62.07-.607.07-.607 1.004.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.913.831.09-.647.347-1.087.633-1.337-2.22-.25-4.555-1.11-4.555-4.942 0-1.092.388-1.983 1.03-2.683-.113-.253-.45-1.27.087-2.647 0 0 .837-.268 2.75 1.025.8-.223 1.65-.332 2.5-.338.85.006 1.7.115 2.5.338 1.9-1.293 2.737-1.025 2.737-1.025.538 1.378.2 2.394.1 2.647.638.7 1.025 1.591 1.025 2.683 0 3.842-2.337 4.688-4.562 4.933.35.3.675.914.675 1.85 0 1.339-.013 2.414-.013 2.739 0 .262.175.575.688.475A9.966 9.966 0 0 0 20 10.247c0-5.522-4.477-10-10-10Z"/>
                    </svg>
                    <span>34k+</span>
                </a>
                <button type="button" class="theme-toggle" aria-label="Switch to dark mode" aria-pressed="false">
                    <svg class="theme-toggle__moon" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M18.59 12.419c.137-.137.268-.278.394-.425A9.109 9.109 0 1 1 8.005 1.015 7.784 7.784 0 1 0 18.59 12.419Z"/>
                    </svg>
                    <svg class="theme-toggle__sun" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10 3.055a.586.586 0 0 0 .586-.586V.586a.586.586 0 1 0-1.172 0v1.883c0 .323.262.586.586.586ZM4.26 5.089a.586.586 0 1 0 .829-.829L3.758 2.93a.586.586 0 0 0-.829.829l1.331 1.33Zm0 9.822-1.33 1.332a.586.586 0 1 0 .828.828L5.09 15.74a.586.586 0 1 0-.829-.829ZM10 16.945a.586.586 0 0 0-.586.586v1.883a.586.586 0 0 0 1.172 0v-1.883a.586.586 0 0 0-.586-.586ZM3.055 10a.586.586 0 0 0-.586-.586H.586a.586.586 0 0 0 0 1.172h1.883A.586.586 0 0 0 3.055 10ZM10 5.02A4.985 4.985 0 0 0 5.02 10 4.985 4.985 0 0 0 10 14.98 4.985 4.985 0 0 0 14.98 10 4.985 4.985 0 0 0 10 5.02Zm5.74.069 1.331-1.332a.586.586 0 1 0-.828-.828L14.91 4.26a.586.586 0 1 0 .829.829Zm0 9.822a.586.586 0 0 0-.829.829l1.331 1.331a.586.586 0 0 0 .829-.829l-1.331-1.331Zm3.674-5.497h-1.883a.586.586 0 0 0 0 1.172h1.883a.586.586 0 1 0 0-1.172Z"/>
                    </svg>
                </button>
                <button type="button" class="navbar-toggle" data-toggle="collapse" aria-label="Toggle navigation" data-target=".navbar-collapse"><i
                        class="fa fa-inverse fa-bars"></i></button>
            <?php endif; ?>
        </div>

        <div class="navbar-collapse collapse navbar-right">
            <?php

            // main navigation
            echo Nav::widget([
                'id' => 'main-nav',
                'encodeLabels' => false,
                'options' => ['class' => 'nav navbar-nav navbar-main-menu'],
                'activateItems' => true,
                'activateParents' => true,
                'dropDownCaret' => '<span class="caret"></span>',
                'items' => [
                    [
                        'label' => 'News',
                        'url' => ['news/index'],
                        'active' => $controller === 'news'
                    ],
                    [
                        'label' => 'Guide',
                        'url' => ['guide/entry'],
                        'options' => ['title' => 'The Definitive Guide to Yii'],
                        'active' => $controller === 'guide' && strncmp((string)$action, 'extension-', 10) !== 0
                    ],
                    [
                        'label' => 'API',
                        'url' => ['api/entry'],
                        'options' => ['title' => 'API Documentation'],
                        'active' => $controller === 'api' && strncmp((string)$action, 'extension-', 10) !== 0
                    ],
                    [
                        'label' => 'Wiki',
                        'url' => ['wiki/index'],
                        'options' => ['title' => 'Community Wiki'],
                        'active' => $controller === 'wiki'
                    ],
                    [
                        'label' => 'Forum',
                        'url' => '@web/forum',
                        'options' => ['title' => 'Community Forum'],
                        'active' => $controller === 'forum'
                    ],
                    [
                        'label' => 'Community',
                        'items' => [
                            [
                                'label' => 'Places',
                                'url' => ['site/community']
                            ],
                            [
                                'label' => 'Members',
                                'url' => ['/user/index'],
                                'options' => ['title' => 'Community Members'],
                                'active' => $controller === 'user' && in_array($action, ['index', 'view'])
                            ],
                            [
                                'label' => 'Hall of Fame',
                                'url' => ['/user/halloffame'],
                                'options' => ['title' => 'Community Hall of Fame']
                            ],
                            [
                                'label' => 'Badges',
                                'url' => ['/badges'],
                                'options' => ['title' => 'Community Badges'],
                                'active' => $controller === 'user' && in_array($action, ['badges', 'view-badge'])
                            ],
                        ],
                    ],
                    ['label' => 'More', 'items' => [
                        ['label' => 'Learn', 'options' => ['class' => 'separator']],
                        ['label' => 'Books', 'url' => ['site/books']],
                        ['label' => 'Resources', 'url' => ['site/resources']],
                        ['label' => 'Develop', 'options' => ['class' => 'separator']],
                        ['label' => 'Download Yii', 'url' => ['site/download']],
                        [
                            'label' => 'Extensions',
                            'url' => ['extension/index'],
                            'options' => ['title' => 'Extensions'],
                            'active' => $controller === 'extension' || strncmp($action, 'extension-', 10) === 0
                        ],
                        ['label' => 'Report an Issue', 'url' => ['site/report-issue']],
                        ['label' => 'Report a Security Issue', 'url' => ['site/security']],
                        ['label' => 'Contribute to Yii', 'url' => ['/site/contribute']],
                        ['label' => 'Donate', 'url' => ['site/donate']],
                        ['label' => 'About', 'options' => ['class' => 'separator']],
                        ['label' => 'Release Cycle', 'url' => ['site/release-cycle']],
                        ['label' => 'License', 'url' => ['site/license']],
                        ['label' => 'Team', 'url' => ['site/team']],
                        ['label' => 'Official Logos and Design', 'url' => ['site/logo']],
                    ]],
                ],
            ]);
            ?>

            <?php if (!$discourse): ?>
            <div class="navbar-utilities">
                <div class="navbar-search">
                    <?= $this->render('_searchForm') ?>
                </div>

                <nav class="navbar-account" aria-label="Account">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <?= Html::a('Login', ['/auth/login'], ['class' => 'navbar-account__login']) ?>
                    <?php else: ?>
                        <div class="dropdown navbar-account__dropdown">
                            <button class="navbar-account__trigger" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
                                </svg>
                                <span><?= Html::encode(Yii::$app->user->identity->username) ?></span>
                                <span class="caret" aria-hidden="true"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><?= Html::a('Profile', ['/user/profile']) ?></li>
                                <li>
                                    <?= Html::beginForm(['/auth/logout'], 'post', ['class' => 'navbar-account__logout-form']) ?>
                                    <?= Html::submitButton('Logout', ['class' => 'navbar-account__logout']) ?>
                                    <?= Html::endForm() ?>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>

            <?php endif; ?>

        </div>
    </div>
</header>
