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
    <?= Html::cssFile('@web/css/all.css') ?>

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
                'class' => 'navbar-default navbar-inverse',
            ],
        ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'activateItems' => false,
                'items' => [
                    ['label' => 'Home', 'url' => ['site/index']],
                    ['label' => 'Learn', 'items' => [
                        ['label' => 'The Definitive Guide', 'url' => ['guide/index', 'version' => '2.0', 'language' => 'en']],
                        ['label' => 'Class Reference', 'url' => ['api/index', 'version' => '2.0']],
                        ['label' => 'Tutorials', 'url' => 'https://yiicamp.com/tutorials'],
                        ['label' => 'Answers', 'url' => 'https://yiicamp.com/answers'],
                        ['label' => 'Books', 'url' => ['site/books']],
                    ]],
                    ['label' => 'Develop', 'items' => [
                        ['label' => 'Install Yii', 'url' => ['site/install']],
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
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Yii Software LLC <?= date('Y') ?></p>
        </div>
    </footer>

    <?= Html::jsFile('@web/js/all.js') ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
