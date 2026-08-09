<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var Doc $doc
 */

use app\models\Doc;
use app\models\GuideSection;
use app\widgets\SearchForm;
use yii\helpers\Html;

$this->beginContent('@app/views/guide/partials/_guide-layout.php', [
    'guide' => $guide,
]);
$this->title = 'Page not found';
?>
    <main class="content docs-error docs-error--embedded">
        <div class="docs-error__hero">
            <div class="docs-error__status" aria-hidden="true">404</div>
            <div class="docs-error__intro">
                <h1>Not in this guide.</h1>
                <p>The page may have moved, or it may belong to another version or translation.</p>

                <?php if (!isset($extension)): // TODO search currently does not work for extensions ?>
                    <div class="docs-error__search">
                        <?= SearchForm::widget([
                            'type' => 'guide',
                            'placeholder' => 'Search the guide…',
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

            <?php

            /** @var GuideSection[][] $alternatives */
            $alternatives = $guide->findSectionInOtherLanguages($section->name);
            if (!empty($alternatives)): ?>
                <section class="docs-error__recovery">
                <h2>This page exists elsewhere</h2>
                <p>Open it in another available version or language.</p>
                <ul class="docs-error__options">
                <?php foreach($alternatives as $version => $altSections) {
                    $links = [];
                    echo '<li><strong>Version ' . Html::encode($version) . '</strong><div>';
                    foreach($altSections as $altSection) {
                        if (isset($extensionName, $extensionVendor)) {
                            $url = ['guide/extension-view', 'section' => $altSection->name, 'version' => $altSection->guide->version, 'language' => $altSection->guide->language, 'name' => $extensionName, 'vendorName' => $extensionVendor];
                        } else {
                            $url = ['guide/view', 'section' => $altSection->name, 'version' => $altSection->guide->version, 'language' => $altSection->guide->language, 'type' => $altSection->guide->getTypeUrlName()];
                        }
                        $linkName = $altSection->guide->getLanguageOptions()[$altSection->guide->language] ?? 'Unknown';
                        if ($altSection->guide->language === 'en') {
                            $links[$altSection->guide->language] = '<strong>' . Html::a($linkName, $url) . '</strong>';
                        } else {
                            $links[$altSection->guide->language] = Html::a($linkName, $url);
                        }
                    }
                    ksort($links);
                    echo implode(', ', $links);
                    echo '</div></li>';
                }
                ?>
                </ul>
                </section>
            <?php endif; ?>
    </main>

<?php $this->endContent() ?>
