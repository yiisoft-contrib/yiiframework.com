<?php

/* @var $this yii\web\View */
use app\models\ApiPrimitive;
use app\models\ApiType;
use yii\helpers\Url;

/* @var $model app\models\ApiType|app\models\ApiPrimitive */

?>
<div class="search-result">
    <div class="row">
        <!--div class="col-sm-5"><img src="image/image_01.jpg" class="img-responsive" alt=""></div-->

        <div class="col-sm-12">
            <h3>
                <a href="<?= Url::to($model->getUrl()) ?>">
                    <span class="label label-warning"><?= $model->type ?></span>
                    <span class="label label-info"><?= $model->version ?></span>
                    <?php if ($model instanceof ApiType) {
                        echo $model->name; // TODO add extends, implements, uses etc..
                    } elseif ($model instanceof ApiPrimitive) {
                        echo $model->definedBy . '::' . $model->name;
                    } ?>
                </a>

                <span class="pull-right">


                </span>
            </h3>
            <?php
                $highlight = $model->getHighlight();
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
            ?>
        </div>

    </div>
    <!--div class="search-info"><span>Technologies</span> - <span>11/10/2014</span> - <span><a href="#">John Doe</a></span></div-->
</div>
