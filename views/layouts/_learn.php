<?php
/**
 *
 */
use yii\helpers\Html;

?>
<li>
    <div class="yamm-content">
        <div class="row">
            <div class="col-sm-4">
                <?php $url = ['guide/view', 'section' => 'start-installation', 'language' => 'en', 'version' => key(Yii::$app->params['guide.versions'])]; ?>
                <h3><?= Html::a('Getting started', $url) ?></h3>

                <p>Get your first Yii application up and running by following the "Getting started" instructions of the guide.</p>

                <p><?= Html::a('Get started!', $url, ['class' => 'btn btn-primary']) ?></p>
            </div>
            <div class="col-sm-4">
                <?php $url = ['guide/index', 'language' => 'en', 'version' => key(Yii::$app->params['guide.versions'])]; ?>
                <h3><?= Html::a('The Definitive Guide', $url) ?></h3>

                <p>This is the most comprehensive Yii documentation that gives the definitive description of every feature of Yii.</p>

                <p><?= Html::a('The Definitive Guide', $url, ['class' => 'btn btn-primary']) ?></p>
            </div>
            <div class="col-sm-4">
                <h3><?= Html::a('API Documentation', ['api/index', 'version' => reset(Yii::$app->params['api.versions'])]) ?></h3>

                <p>Here you find detailed information about the Yii Framework classes, their methods and properties.</p>

                <p><?= Html::a('API Documentation', ['api/index', 'version' => reset(Yii::$app->params['api.versions'])], ['class' => 'btn btn-primary']) ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <h3><?= Html::a('Tutorials', 'https://yiicamp.com/tutorials') ?> <small><span class="label label-warning">coming soon</span></small></h3>

                <p>On yiicamp.com you find a lot of tutorials and guides that help you create specific...TODO</p>

                <p><?= Html::a('Tutorials on yiicamp.com', 'https://yiicamp.com/tutorials', ['class' => 'btn btn-primary']) ?></p>
            </div>
            <div class="col-sm-4">
                <h3><?= Html::a('Answers', 'https://yiicamp.com/answers') ?> <small><span class="label label-warning">coming soon</span></small></h3>

                <p>If you are looking for the answer to a specific problem you'll find it on Answers on yiicamp.com.</p>

                <p><?= Html::a('Answers on yiicamp.com', 'https://yiicamp.com/answers', ['class' => 'btn btn-primary']) ?></p>
            </div>
            <div class="col-sm-4">
                <h3><?= Html::a('Books', ['site/books']) ?></h3>

                <p>There are handy books about both Yii 2.0 and Yii 1.1.</p>

                <p><?= Html::a('Books', ['site/books'], ['class' => 'btn btn-primary']) ?></p>
            </div>
        </div>
    </div>
</li>