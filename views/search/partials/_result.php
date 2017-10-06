<?php

use app\models\search\SearchApiType;
use app\models\search\SearchApiPrimitive;
use app\models\search\SearchGuideSection;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\search\SearchActiveRecord */

$encodeHighlight = function($h) {
    return strip_tags($h, '<em>');
}

?>
<div class="search-result">
    <div class="row">
        <div class="col-sm-12">
            <h3>
                <a href="<?= Url::to($model->getUrl()) ?>" class="title"><?php
                        echo $model->getTitle();
                ?></a>
                <a href="<?= Url::to($model->getUrl()) ?>" class="label label-warning"><?= $model->type ?></a>
                <a href="<?= Url::to($model->getUrl()) ?>" class="label label-info"><?= $model->version ?></a>
                <?php if (isset($model->language)): ?>
                    <a href="<?= Url::to($model->getUrl()) ?>" class="label label-success"><?= $model->language ?></a>
                <?php endif; ?>
                <?php if (YII_DEBUG) {
                    echo "<small>score: " . $model->getScore() . "</small>";
                } ?>
            </h3>
            <?php
                $highlight = $model->getHighlight();
//                echo "<pre>" . print_r($highlight, true) . "</pre>";

                if (!empty($highlight['shortDescription'])) {
                    echo '<p><strong>' . $encodeHighlight(reset($highlight['shortDescription'])) . '</strong></p>';
                }
                if (!empty($highlight['content'])) {
                    echo '<p>...' . $encodeHighlight(implode('...', $highlight['content'])) . '...</p>';
                }

            // TODO sanitize output!

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
    <!--div class="search-info"><span>Technologies</span> - <span>11/10/2014</span> - <span><a href="#">John Doe</a></span></div-->
</div>
