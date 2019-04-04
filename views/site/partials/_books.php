<?php
/** @var $books array */

use yii\helpers\Html;

?>

<div class="books-list">
    <div class="row">
		<?php foreach ($books as $i => $book): ?>
        <div class="col-md-6 col-12 books-item">
            <div class="row">
                <div class="col-12 col-sm-2 col-md-4 books-image">
                    <a href="<?= $book['url'] ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?= Yii::getAlias(Html::encode($book['image'])) ?>"
                             alt="<?= Html::encode($book['title']) ?>" class="thumbnail img-fluid"/>
                    </a>
                </div>
                <div class="col-12 col-sm-10 col-md-8 books-info">
                    <div class="books-link">
                        <a href="<?= $book['url'] ?>" target="_blank"
                           rel="noopener noreferrer"><?= Html::encode($book['title']) ?></a>
                        <span class="skill <?= Html::encode($book['level']) ?>"
                              title="<?= Html::encode($book['level-description']) ?>">&#9632;</span>
                    </div>
                    <div class="author-publisher">
                        <div><span class="what">Author: </span> <span
                                    class="who"><?= Html::encode($book['author']) ?></span></div>
                    </div>
                    <p class="books-description">
						<?= $book['description'] ?>
                    </p>
                </div>
            </div>
        </div>
		<?php if ($i % 2 === 1) : ?>
    </div>
    <div class="row">
		<?php endif ?>
		<?php endforeach ?>
    </div>
</div>