<?php
use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\Url;

/* @var $news app\models\News[] */

?>
<div class="container content-separator latest-news">
    <div class="row">
        <div class="col-md-12">
            <div class="dashed-heading-front">
                <span>Latest News</span>
            </div>

            <div class="row news">
                <?php
                $i = 0;
                foreach($news as $newsItem): ?>

                    <div class="col-md-6">
                        <span class="date"><?= Yii::$app->formatter->asDate($newsItem->news_date) ?></span>
                        <h2><?= Html::a(Html::encode($newsItem->title), ['news/view', 'id' => $newsItem->id, 'name' => $newsItem->slug]) ?></h2>
                        <div class="text">
                            <p><?= Markdown::process($newsItem->getTeaser(), 'gfm') ?></p>
                        </div>
                    </div>

                    <?php if (++$i % 2 == 0 && $i != count($news)) {
                        echo '</div><div class="row news">';
                    } ?>

                <?php endforeach; ?>
            </div>

            <a href="<?= Url::to(['news/index']) ?>" class="btn btn-front btn-block">Read all news</a>
        </div>
    </div>
</div>
