<?php

use app\models\search\SearchApiType;
use app\models\search\SearchExtension;
use app\models\search\SearchGuideSection;
use app\models\search\SearchNews;
use app\models\search\SearchWiki;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this  */
/** @var app\models\search\SearchActiveRecord $model */

$encodeHighlight = static function($h) {
    return strip_tags($h, '<em>');
};

$highlight = $model->getHighlight();

if ($model->getUrl() === null) {
    // do not display search results that have been removed from the
    // site but are still in the search index (not yet removed)
    return;
}

?>
<div class="search-result">
    <div class="row">
        <div class="col-sm-12">
            <h3>
                <a href="<?= Url::to($model->getUrl()) ?>" class="title"><?php
                    if ($model instanceof SearchApiType) {
                        if (isset($highlight['name'])) {
                            echo $encodeHighlight(($model->namespace ? $model->namespace . '\\' : '') . implode(' ... ', $highlight['name']));
                        } else {
                            echo Html::encode(($model->namespace ? $model->namespace . '\\' : '') . $model->name);
                        }
                    } elseif ($model instanceof SearchExtension) {
                        if (isset($highlight['name'])) {
                            echo $encodeHighlight(implode(' ... ', $highlight['name']));
                        } else {
                            echo Html::encode($model->name);
                        }
                    } elseif (isset($highlight['title'])) {
                        echo $encodeHighlight(implode(' ... ', $highlight['title']));
                    } elseif (isset($highlight['title.stemmed'])) {
                        echo $encodeHighlight(implode(' ... ', $highlight['title.stemmed']));
                    } else {
                        echo Html::encode($model->getTitle());
                    }
                ?></a>
                <a href="<?= Url::to($model->getUrl()) ?>" class="label label-warning"><?= Html::encode(ucfirst($model->type)) ?></a>
                <?php if ($model instanceof SearchExtension || $model instanceof SearchWiki || $model instanceof SearchGuideSection): ?>
                    <a href="<?= Url::to($model->getUrl()) ?>" class="label label-default"><?= Html::encode($model->category) ?></a>
                <?php endif; ?>
                <?php if (!$model instanceof SearchNews): ?>
                <a href="<?= Url::to($model->getUrl()) ?>" class="label label-info"><?= Html::encode($model->version) ?></a>
                <?php endif; ?>
                <?php if (isset($model->language)): ?>
                    <a href="<?= Url::to($model->getUrl()) ?>" class="label label-success"><?= Html::encode($model->language) ?></a>
                <?php endif; ?>
                <?php if (YII_DEBUG) {
                    echo "<small>score: " . $model->getScore() . "</small>";
                } ?>
            </h3>
            <?php
                if ($model instanceof SearchApiType || $model instanceof SearchExtension) {
                    echo '<p><strong>';
                    if (isset($highlight['title'])) {
                        echo $encodeHighlight(implode(' ... ', $highlight['title']));
                    } elseif (isset($highlight['title.stemmed'])) {
                        echo $encodeHighlight(implode(' ... ', $highlight['title.stemmed']));
                    } else {
                        echo Html::encode($model->getDescription());
                    }
                    echo '</strong></p>';
                }
                if (isset($highlight['content'])) {
                    echo '<p>... ' . $encodeHighlight(implode('...', $highlight['content'])) . ' ...</p>';
                } elseif (isset($highlight['content.stemmed'])) {
                    echo '<p>... ' . $encodeHighlight(implode('...', $highlight['content.stemmed'])) . ' ...</p>';
                } elseif (!$model instanceof SearchApiType) {
                    echo '<p>' . Html::encode($model->getDescription()) . '</p>';
                }

//                if (!in_array($model->type, ['property', 'const', 'event'])) {
//                    if (!empty($highlight['content'])) {
//                        echo '<p>...' . implode('...', $highlight['content']) . '...</p>';
//                    } elseif ($model->canGetProperty('description')) {
//                        echo '<p>' . Html::encode(StringHelper::truncateWords($model->description, 100)) . '</p>';
//                    }
//                }
            ?>
        </div>

    </div>
</div>
