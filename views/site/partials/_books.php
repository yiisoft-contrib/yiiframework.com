<?php
/** @var $books array */

use yii\helpers\Html;

?>

<div class="books-list">
<?php foreach ($books as $i => $book): ?>
    <?php if ($i !== 0 && $i % 2 === 0): ?>
        <div class="clearfix visible-md"></div>
    <?php endif ?>

    <?php if ($i !== 0 && $i % 5 === 0): ?>
        <div class="clearfix visible-lg"></div>
    <?php endif ?>

    <div
        class="col-lg-5ths col-md-6 col-xs-12 books-item">
        <div class="books-image">
            <a href="<?= $book['url'] ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>"
                     alt="<?= Html::encode($book['title']) ?>" class="thumbnail img-responsive"/>
            </a>
        </div>
        <div class="books-link">
            <a href="<?= $book['url'] ?>" target="_blank" rel="noopener noreferrer"><?= Html::encode($book['title']) ?></a>
            <span class="skill <?= Html::encode($book['level']) ?>" title="<?= Html::encode($book['level-description']) ?>">&#9632;</span>
        </div>
        <div class="author-publisher">
            <div><span class="what">Author: </span> <span class="who"><?= Html::encode($book['author']) ?></span></div>
        </div>
        <p class="books-description">
            <?= $book['description'] ?>
        </p>
    </div>
<?php endforeach ?>
</div>