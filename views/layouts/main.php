<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use app\assets\AppAsset;
AppAsset::register($this);

/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <link rel="shortcut icon" href="<?= Yii::getAlias('@web/favicon.ico') ?>" />
        <link rel="apple-touch-icon" sizes="57x57" href="<?= Yii::getAlias('@web/apple-icon-57x57.png') ?>">
        <link rel="apple-touch-icon" sizes="60x60" href="<?= Yii::getAlias('@web/apple-icon-60x60.png') ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= Yii::getAlias('@web/apple-icon-72x72.png') ?>">
        <link rel="apple-touch-icon" sizes="76x76" href="<?= Yii::getAlias('@web/apple-icon-76x76.png') ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= Yii::getAlias('@web/apple-icon-114x114.png') ?>">
        <link rel="apple-touch-icon" sizes="120x120" href="<?= Yii::getAlias('@web/apple-icon-120x120.png') ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= Yii::getAlias('@web/apple-icon-144x144.png') ?>">
        <link rel="apple-touch-icon" sizes="152x152" href="<?= Yii::getAlias('@web/apple-icon-152x152.png') ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::getAlias('@web/apple-icon-180x180.png') ?>">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?= Yii::getAlias('@web/android-icon-192x192.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::getAlias('@web/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= Yii::getAlias('@web/favicon-96x96.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::getAlias('@web/favicon-16x16.png') ?>">
        <link rel="manifest" href="<?= Yii::getAlias('@web/manifest.json') ?>">
        <meta name="msapplication-TileColor" content="#fff">
        <meta name="msapplication-TileImage" content="<?= Yii::getAlias('@web/ms-icon-144x144.png') ?>">
        <meta name="theme-color" content="#fff">

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <?= Html::csrfMetaTags() ?>
        <?php $this->registerJs('yiiBaseUrl = ' . \yii\helpers\Json::htmlEncode(Yii::$app->request->getBaseUrl()), \yii\web\View::POS_HEAD); ?>

        <title><?php if (!empty($this->title)): ?><?= Html::encode($this->title) ?> - <?php endif?>Yii PHP Framework</title>

        <meta property="og:site_name" content="Yii Framework" />
        <meta property="og:title" content="<?= !empty($this->title) ? Html::encode($this->title) : 'Yii Framework' ?>" />
        <meta property="og:image" content="<?= Url::to(Yii::getAlias('@web/image/facebook_cover.png'), true) ?>" />
        <meta property="og:url" content="<?= Url::to() ?>" />
        <meta property="og:description" content="" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="yiiframework" />
        <meta name="twitter:title" content="<?= !empty($this->title) ? Html::encode($this->title) : 'Yii Framework' ?>" />
        <meta name="twitter:description" content="" />
        <meta name="twitter:image" content="<?= Url::to(Yii::getAlias('@web/image/twitter_cover.png'), true) ?>" />
        <meta name="twitter:image:width" content="120" />
        <meta name="twitter:image:height" content="120" />

        <?php $this->head() ?>
    </head>
    <body data-spy="scroll" data-target="#scrollnav" data-offset="1">
        <?php $this->beginBody() ?>

        <div id="page-wrapper" class="">

            <header class="navbar navbar-inverse navbar-static" id="top">
                <div class="container">
                    <div id="main-nav-head" class="navbar-header">
                        <a href="<?= Yii::$app->homeUrl ?>" class="navbar-brand">
                            <img src="<?= Yii::getAlias('@web/image/logo.svg') ?>"
                                alt="Yii PHP Framework"
                                width="165" height="35" />
                        </a>
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><i class="fa fa-inverse fa-bars"></i></button>
                    </div>

                    <div class="navbar-collapse collapse navbar-right">
                        <?php

                        // main navigation
                        $controller = Yii::$app->controller ? Yii::$app->controller->id : null;
                        $action = Yii::$app->controller && Yii::$app->controller->action ? Yii::$app->controller->action->id : null;
                        echo Nav::widget([
                            'id' => 'main-nav',
                            'encodeLabels' => false,
                            'options' => ['class' => 'nav navbar-nav navbar-main-menu'],
                            'activateItems' => true,
                            'activateParents' => true,
                            'dropDownCaret' => '<span class="caret"></span>',
                            'items' => [
                                ['label' => 'Guide', 'url' => ['guide/entry'], 'options' => ['title' => 'The Definitive Guide to Yii'], 'active' => ($controller == 'guide')],
                                ['label' => 'API', 'url' => ['api/index', 'version' => reset(Yii::$app->params['versions']['api'])], 'options' => ['title' => 'API Documentation'], 'active' => ($controller == 'api')],
                                ['label' => 'Wiki', 'url' => ['wiki/index'], 'options' => ['title' => 'Community Wiki']],
                                ['label' => 'Extensions', 'url' => ['extension/index'], 'options' => ['title' => 'Extensions']],
                                ['label' => 'Community', 'items' => [
                                    ['label' => 'Forum', 'url' => '@web/forum', 'options' => ['title' => 'Community Forum']],
                                    ['label' => 'Live Chat', 'url' => ['site/chat']],
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
                                    //['label' => '<i class="fa fa-angle-double-right"></i>Extensions<span class="label label-warning">coming soon</span>', 'url' => 'https://yiicamp.com/extensions'],
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
                                //['label' => 'Login', 'url' => ['auth/login'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg']],
                                //['label' => 'Signup', 'url' => ['auth/signup'], 'visible' => Yii::$app->user->isGuest, 'options' => ['class' => 'hidden-lg']],
                                //['label' => 'Logout', 'url' => ['auth/logout'], 'visible' => !Yii::$app->user->isGuest, 'linkOptions' => ['data-method' => 'post'], 'options' => ['class' => 'hidden-lg']],
                            ],
                        ]);
                        ?>
                        <div class="nav navbar-nav navbar-right">
                        <?php
                            echo Nav::widget([
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
                            <?= $this->render('partials/_searchForm'); ?>
                        </div>
                    </div>
                </div>
            </header>

            <?= $content ?>

            <?= $this->render('partials/_footer'); ?>

        </div> <!-- close the id="page-wrapper" -->

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
