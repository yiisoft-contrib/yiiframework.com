<?php

/* @var $this yii\web\View */
use app\models\SearchApiPrimitive;
use app\models\SearchApiType;
use app\models\SearchGuideSection;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $model app\models\ApiType|app\models\SearchApiPrimitive */

?>
<div class="search-result">
    <div class="row">
        <div class="col-sm-12">
            <h3>
                <a href="<?= Url::to($model->getUrl()) ?>" class="title"><?php
                    if ($model instanceof SearchApiType) {
                        echo $model->name; // TODO add extends, implements, uses etc..
                    } elseif ($model instanceof SearchApiPrimitive) {
                        echo $model->definedBy . '::' . $model->name;
                    } elseif ($model instanceof SearchGuideSection) {
                        echo $model->title;
                    }
                ?></a>
                <a href="<?= Url::to($model->getUrl()) ?>" class="label label-warning"><?= $model->type ?></a>
                <a href="<?= Url::to($model->getUrl()) ?>" class="label label-info"><?= $model->version ?></a>
                <?php if (isset($model->language)): ?>
                    <a href="<?= Url::to($model->getUrl()) ?>" class="label label-success"><?= $model->language ?></a>
                <?php endif; ?>
            </h3>
            <?php
                $highlight = $model->getHighlight();
                if ($model instanceof SearchGuideSection) {
                    if (!empty($highlight['body'])) {
                        echo '<p>...' . implode('...', $highlight['body']) . '...</p>';
                    }
                } else {
                    if (!empty($highlight['shortDescription'])) {
                        echo '<p><strong>' . reset($highlight['shortDescription']) . '</strong></p>';
                    } else {
                        echo '<p><strong>' . Html::encode($model->shortDescription) . '</strong></p>';
                    }
                    if (!in_array($model->type, ['property', 'const', 'event'])) {
                        if (!empty($highlight['description'])) {
                            echo '<p>...' . implode('...', $highlight['description']) . '...</p>';
                        } else {
                            echo '<p>' . Html::encode(StringHelper::truncateWords($model->description, 100)) . '</p>';
                        }
                    }
                }
            ?>
        </div>

    </div>
    <!--div class="search-info"><span>Technologies</span> - <span>11/10/2014</span> - <span><a href="#">John Doe</a></span></div-->
</div>
