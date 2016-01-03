<?php
/* @var $books array */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="container content-separator wow fadeInUp books-videos">
    <div class="row">
        <div class="col-md-12">
            <div class="dashed-heading-front-books">
                <span>Books and Videos on Yii</span>
            </div>
            <div class="books-videos-description">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <?php foreach ($books as $book): ?>
                <div class="col-md-3 col-sm-6 col-xs-6 books-item">
                    <div class="books-image">
                        <a href="<?= $book['url'] ?>">
                            <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>" alt="<?= Html::encode($book['title'])?>" class="thumbnail img-responsive"/>
                        </a>
                    </div>
                    <div class="books-link">
                        <a href="<?= $book['url'] ?>"><?= Html::encode($book['title'])?></a>
                    </div>
                    <div class="author-publisher">
                        <div><span class="what">Author: </span> <span class="who"><?= Html::encode($book['author'])?></span></div>
                    </div>
                    <p class="books-description">
                        <?= $book['description'] ?>
                    </p>
                </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
    <div class="row see-all">
        <a href="<?= Url::to(['site/books']) ?>" class="btn btn-front btn-block">See all books</a>
    </div>
</div>
