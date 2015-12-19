<?php

/* @var $this yii\web\View */
use app\models\SearchApiPrimitive;
use app\models\SearchApiType;
use app\models\SearchGuideSection;
use yii\helpers\Url;
use yii\helpers\Markdown;

/* @var $model app\models\ApiType|app\models\SearchApiPrimitive */

?>
<div class="search-result">
    <div class="row">
        <!--div class="col-sm-5"><img src="image/image_01.jpg" class="img-responsive" alt=""></div-->

        <div class="col-sm-12">
            <h3>
                <a href="<?= Url::to($model->getUrl()) ?>">
                    <?php if ($model instanceof SearchApiType) {
                        echo $model->name; // TODO add extends, implements, uses etc..
                    } elseif ($model instanceof SearchApiPrimitive) {
                        echo $model->definedBy . '::' . $model->name;
                    } elseif ($model instanceof SearchGuideSection) {
                        echo $model->title;
                    } ?>
                    <span class="label label-warning"><?= $model->type ?></span>
                    <span class="label label-info"><?= $model->version ?></span>
                    <?php if (isset($model->language)): ?>
                        <span class="label label-success"><?= $model->language ?></span>
                    <?php endif; ?>
                </a>

                <span class="pull-right">


                </span>
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
                        echo '<p><strong>' . $model->shortDescription . '</strong></p>';
                    }
                    if (!in_array($model->type, ['property', 'const', 'event'])) {
                        if (!empty($highlight['description'])) {
                            echo '<p>...' . implode('...', $highlight['description']) . '...</p>';
                        } else {
                            echo '<p>' . \yii\helpers\StringHelper::truncateWords($model->description, 100) . '</p>';
                        }
                    }
                }
            ?>
        </div>

    </div>
    <!--div class="search-info"><span>Technologies</span> - <span>11/10/2014</span> - <span><a href="#">John Doe</a></span></div-->
</div>
