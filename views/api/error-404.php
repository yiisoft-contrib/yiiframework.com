<?php
/**
 * @var $this yii\web\View
 * @var $alternatives array
 * @var $alternativeVersions array
 */

use app\widgets\SearchForm;
use yii\helpers\Html;

$this->title = 'Not Found (#404)';
?>
<?= $this->render('//site/partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="site-error content">

        <div class="alert alert-warning">
            <p><strong>Sorry, we could not find this class in the API documentation.</strong></p>

            <?php if (!empty($alternatives)): ?>

                <p>This class is only available in the following versions:</p>

                <ul>
                <?php foreach($alternatives as $version => $url): ?>
                    <li><?= Html::a("Version $version", $url) ?></li>
                <?php endforeach; ?>
                </ul>

            <?php endif; ?>

            <p>The API documentation <?= isset($extension) ? ' for ' . Html::encode($extension->name) : '' ?> is available in the following versions:</p>

            <ul>
                <?php foreach ($alternativeVersions as $version => $url): ?>
                    <li><?= Html::a("Version $version", $url) ?></li>
                <?php endforeach; ?>
            </ul>


            <?php if (!isset($extension)): // TODO search currently does not work for extensions
             ?>

            <p>You may also try searching the API documentation:</p>

            <?= SearchForm::widget([
                'type' => 'api',
                'placeholder' => 'Search API…',
            ]) ?>

            <?php endif; ?>

        </div>

    </div>
</div>
