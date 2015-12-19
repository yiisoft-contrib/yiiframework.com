<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Install Yii';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="row">
        <div class="content site-license">
            <p>Yii is an open source project released under the terms of the <?= Html::a('BSD License', ['site/license']) ?>. This means that you can use Yii for free to develop either open-source or proprietary Web applications.</p>

            <p>Currently there are two major versions of Yii: <a href="#yii2">2.0</a> and <a href="#yii1">1.1</a>.</p>

            <h2 id="yii2">Yii 2.0</h2>


                <h3 id="yii2-docs">Offline Documentation</h3>

                <?php foreach(Yii::$app->params['guide.versions']['2.0'] as $locale => $language): ?>
                <ul>
                    <li><?php
                        echo "$language: ";
                        $file = "yii-docs-2.0-$locale.tar";
                        echo Html::a("$file.gz", ['site/file', 'category' => 'docs-offline', 'file' => "$file.gz"]) . ' ';
                        echo Html::a("$file.bz2", ['site/file', 'category' => 'docs-offline', 'file' => "$file.bz2"]);
                    ?>
                    </li>
                </ul>
                <?php endforeach; ?>

            <h2 id="yii1">Yii 1.1</h2>
        </div>
    </div>
</div>
