<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <?= Html::cssFile(YII_DEBUG ? '@web/css/all.css' : '@web/css/all.min.css?v=' . filemtime(Yii::getAlias('@webroot/css/all.min.css'))) ?>

    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <?php
        NavBar::begin([
            'brandLabel' => 'Yii Framework',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-default',
            ],
        ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'activateItems' => false,
                'items' => [
                    ['label' => 'Learn', 'items' => [
                        ['label' => 'The Definitive Guide', 'url' => ['guide/index', 'version' => '2.0', 'language' => 'en']],
                        ['label' => 'Class Reference', 'url' => ['api/index', 'version' => '2.0']],
                        ['label' => 'Tutorials', 'url' => 'https://yiicamp.com/tutorials'],
                        ['label' => 'Answers', 'url' => 'https://yiicamp.com/answers'],
                        ['label' => 'Books', 'url' => ['site/books']],
                    ]],
                    ['label' => 'Develop', 'items' => [
                        ['label' => 'Install Yii', 'url' => ['guide/view', 'version' => '2.0', 'language' => 'en', 'section' => 'start-installation']],
                        ['label' => 'Extensions', 'url' => 'https://yiicamp.com/extensions'],
                        ['label' => 'Report an Issue', 'url' => 'https://github.com/yiisoft/yii2/issues/new'],
                        ['label' => 'Contribute to Yii', 'url' => ['/site/contribute']],
                        ['label' => 'Jobs', 'url' => 'https://yiicamp.com/jobs'],
                    ]],
                    ['label' => 'Discuss', 'items' => [
                        ['label' => 'Forum', 'url' => '/forum'],
                        ['label' => 'Live Chat', 'url' => ['site/chat']],
                        ['label' => 'GitHub', 'url' => 'https://github.com/yiisoft/yii2'],
                        ['label' => 'Facebook', 'url' => 'https://www.facebook.com/groups/yiitalk/'],
                        ['label' => 'LinkedIn', 'url' => 'https://www.linkedin.com/groups/yii-framework-1483367'],
                        ['label' => 'Twitter', 'url' => 'https://twitter.com/yiiframework'],
                    ]],
                    ['label' => 'Camp', 'url' => 'https://yiicamp.com']
                ],
            ]);
        NavBar::end();
    ?>

    <div class="container">
        <?= $content ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; Yii Software LLC <?= date('Y') ?> <?php echo Html::a('<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>', ['site/contact']) ?></p>
        </div>
    </footer>

    <?= Html::jsFile(YII_DEBUG ? '@web/js/all.js' : '@web/js/all.min.js?v=' . filemtime(Yii::getAlias('@webroot/js/all.min.js'))) ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
