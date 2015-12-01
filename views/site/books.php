<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    <div class="row">
        <div class="site-books">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>There are handy books about both Yii 2.0 and Yii 1.1 which could help you master the framework.</p>

            <h2>Yii 2.0</h2>
        </div>
    </div>
    <div class="row">
    <?php foreach($books2 as $book): ?>
        <div class="col-sm-3 col-md-3 col-lg-3">
            <div class="info-card">
                <div class="ribbon <?= Html::encode($book['level'])?>"><span><?= Html::encode($book['level-text'])?></span></div>
                <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>" alt="<?= Html::encode($book['title'])?>" />
                <div class="info-card-details animate">
                    <div class="info-card-header">
                        <h1><?= Html::encode($book['title'])?></h1>
                        <h3> by <?= Html::encode($book['author'])?> </h3>
                    </div>
                    <div class="info-card-detail">
                        <p>
                            <?= Html::encode($book['description'])?>
                        </p>
                    </div>
                    <div class="social">
                        <div>
                            <a href="<?= Html::encode($book['url'])?>"><?= Html::encode($book['title'])?></a>
                        </div>
                        <?= Html::encode($book['level-description'])?>
                    </div>
                </div>
            </div>
        </div> <!-- book -->
    <?php endforeach ?>
    </div>
    <div class="row">
        <h2>Yii 1.1</h2>
    </div>
    <div class="row">
        <?php foreach($books1 as $book): ?>
            <div class="col-sm-3 col-md-3 col-lg-3">
                <div class="info-card">
                    <div class="ribbon <?= Html::encode($book['level'])?>"><span><?= Html::encode($book['level-text'])?></span></div>
                    <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>" alt="<?= Html::encode($book['title'])?>" />
                    <div class="info-card-details animate">
                        <div class="info-card-header">
                            <h1><?= Html::encode($book['title'])?></h1>
                            <h3> by <?= Html::encode($book['author'])?> </h3>
                        </div>
                        <div class="info-card-detail">
                            <p>
                                <?= Html::encode($book['description'])?>
                            </p>
                        </div>
                        <div class="social">
                            <div>
                                <a href="<?= Html::encode($book['url'])?>"><?= Html::encode($book['title'])?></a>
                            </div>
                            <?= Html::encode($book['level-description'])?>
                        </div>
                    </div>
                </div>
            </div> <!-- book -->
        <?php endforeach ?>
    </div>
</div>
