<?php
/** @var $books array */

use yii\helpers\Html;

?>

<div class="books-list">
<?php foreach ($books as $book): ?>
    <article class="books-item">
        <div class="books-image">
            <a href="<?= $book['url'] ?>" target="_blank" rel="noopener noreferrer">
                <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>"
                     alt="Cover of <?= Html::encode($book['title']) ?>" loading="lazy">
            </a>
        </div>
        <div class="books-meta">
            <span class="books-level books-level--<?= Html::encode($book['level']) ?>"
                  title="<?= Html::encode($book['level-description']) ?>">
                <?= Html::encode($book['level-text']) ?>
            </span>
        </div>
        <h3 class="books-link">
            <a href="<?= $book['url'] ?>" target="_blank" rel="noopener noreferrer"><?= Html::encode($book['title']) ?></a>
        </h3>
        <p class="books-author">by <?= Html::encode($book['author']) ?></p>
        <p class="books-description">
            <?= $book['description'] ?>
        </p>
    </article>
<?php endforeach ?>
</div>
