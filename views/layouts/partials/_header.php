<?php

/**
 * This file renders the header navigation for the Yii website and also for the Yii Forum based on Discourse.
 *
 * IMPORTANT NOTE: If you change this file, make sure changes are reflected in the Discourse header also!
 *
 * If this file is rendered for Discourse, the $discourse variable is set to `true`.
 */

use yii\helpers\Html;
use yii\bootstrap\Nav;

/* @var $this \yii\web\View */
/* @var $discourse boolean */

if ($discourse) {
    $controller = 'forum';
    $action = 'index';
} else {
    $controller = Yii::$app->controller ? Yii::$app->controller->id : null;
    $action = Yii::$app->controller && Yii::$app->controller->action ? Yii::$app->controller->action->id : null;
}

?>
<header class="navbar navbar-inverse navbar-static" id="top">
    <div class="container">
        <div id="main-nav-head" class="navbar-header">
            <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                <img src="<?= Yii::getAlias('@web/image/logo.svg') ?>"
                     alt="Yii PHP Framework"
                     width="165" height="35"/>
            </a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i
                    class="fa fa-inverse fa-bars"></i></button>
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
                    ['label' => 'Guide', 'url' => ['guide/entry'], 'options' => ['title' => 'The Definitive Guide to Yii'], 'active' => ($controller == 'guide')],
                    ['label' => 'API', 'url' => ['api/entry'], 'options' => ['title' => 'API Documentation'], 'active' => ($controller == 'api')],
                    ['label' => 'Wiki', 'url' => ['wiki/index'], 'options' => ['title' => 'Community Wiki'], 'active' => ($controller == 'wiki')],
                    ['label' => 'Extensions', 'url' => ['extension/index'], 'options' => ['title' => 'Extensions'], 'active' => ($controller == 'extension' || strncmp($action, 'extension-', 10) === 0)],
                    ['label' => 'Community', 'items' => [
                        ['label' => 'Forum', 'url' => '@web/forum', 'options' => ['title' => 'Community Forum'], 'active' => ($controller == 'forum')],
                        ['label' => 'Live Chat', 'url' => ['site/chat']],
                        ['label' => 'Resources', 'url' => ['site/community']],
                        ['label' => 'Members', 'url' => ['/user/index'], 'options' => ['title' => 'Community Members'], 'active' => ($controller == 'user' && in_array($action, ['index', 'view']))],
                        ['label' => 'Hall of Fame', 'url' => ['/user/halloffame'], 'options' => ['title' => 'Community Hall of Fame']],
                        ['label' => 'Badges', 'url' => ['/badges'], 'options' => ['title' => 'Community Badges'], 'active' => ($controller == 'user' && in_array($action, ['badges', 'view-badge']))],
                    ]],
                    ['label' => 'More&hellip;', 'items' => [
                        ['label' => 'Learn', 'options' => ['class' => 'separator']],
                        ['label' => 'Books', 'url' => ['site/books']],
                        ['label' => 'Resources', 'url' => ['site/resources']],
                        ['label' => 'Develop', 'options' => ['class' => 'separator']],
                        ['label' => 'Download Yii', 'url' => ['site/download']],
                        ['label' => 'Report an Issue', 'url' => ['site/report-issue']],
                        ['label' => 'Report a Security Issue', 'url' => ['site/security']],
                        ['label' => 'Contribute to Yii', 'url' => ['/site/contribute']],
                        ['label' => 'About', 'options' => ['class' => 'separator']],
                        ['label' => 'What is Yii?', 'url' => ['guide/view', 'type' => 'guide', 'version' => reset(Yii::$app->params['versions']['api']), 'language' => 'en', 'section' => 'intro-yii']],
                        ['label' => 'News', 'url' => ['news/index'], 'active' => ($controller == 'news')],
                        ['label' => 'License', 'url' => ['site/license']],
                        ['label' => 'Team', 'url' => ['site/team']],
                        ['label' => 'Official logo', 'url' => ['site/logo']],
                    ]],
                ],
            ]);
            ?>

            <?php if (!$discourse): ?>

            <div class="nav navbar-nav navbar-right">
                <?php echo Nav::widget([
                    'id' => 'login-nav',
                    'encodeLabels' => true,
                    'options' => ['class' => 'nav navbar-nav navbar-main-menu'],
                    'activateItems' => false,
                    'dropDownCaret' => '<span class="caret"></span>',
                    'items' => [
                        Yii::$app->user->isGuest ? (
                        ['label' => 'Login', 'url' => ['/auth/login']]
                        ) : (
                            '<li>'
                            . Html::beginForm(['/auth/logout'], 'post', ['class' => 'navbar-form'])
                            . Html::submitButton(
                                'Logout', // (' . Yii::$app->user->identity->username . ')',
                                ['class' => 'btn btn-link']
                            )
                            . Html::a(Html::encode(Yii::$app->user->identity->username), ['/user/profile'], ['class' => 'btn btn-link'])
                            . Html::endForm()
                            . '</li>'
                        ),
                    ]
                ]);
                ?>
            </div>

            <div class="nav navbar-nav navbar-right">
                <?= $this->render('_searchForm'); ?>
            </div>

            <?php endif; ?>

        </div>
    </div>
</header>