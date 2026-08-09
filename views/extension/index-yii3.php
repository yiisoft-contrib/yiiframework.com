<?php

use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var string $version */

$this->title = 'Yii3 packages';

$this->beginBlock('contentSelectors');
echo $this->render('partials/_versions', [
    'currentVersion' => $version,
    'category' => null,
    'tag' => null,
]);
$this->endBlock();
?>

<div class="container yii3-packages-page style_external_links">
    <div class="content">
        <h1>Packages for Yii3</h1>

        <p class="yii3-packages-page__lead">
            Yii3 packages are distributed through Composer rather than through the legacy extension catalog.
        </p>

        <section>
            <h2>Yii3-specific packages</h2>
            <p>
                Look for packages carrying the <strong>yii3</strong> tag on Packagist. These packages are designed
                specifically for Yii3 applications or provide Yii3 integrations.
            </p>
            <p>
                <?= Html::a(
                    'Browse Packagist',
                    'https://packagist.org/?query=&tags=yii3',
                    ['class' => 'btn btn-primary']
                ) ?>
            </p>
        </section>

        <section>
            <h2>Regular PHP packages</h2>
            <p>
                You are not limited to packages made specifically for Yii3. Regular framework-independent PHP
                packages can be installed and used in a Yii3 application through Composer, provided their PHP and
                dependency requirements are compatible with your project.
            </p>
            <p><?= Html::a('Search all packages on Packagist', 'https://packagist.org/') ?></p>
        </section>
    </div>
</div>
