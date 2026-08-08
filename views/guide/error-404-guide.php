<?php
/**
 * @var $this yii\web\View
 * @var $section string
 * @var $version string
 * @var $language string
 */

use app\models\Guide;
use app\models\GuideSection;
use app\widgets\SearchForm;
use yii\helpers\Html;

$this->title = 'Page not found';
?>
<main class="container content guide-error-page">
    <div class="guide-error-page__hero">
        <div class="guide-error-page__status" aria-hidden="true">404</div>
        <div class="guide-error-page__intro">
            <h1>Not in this guide.</h1>
            <p>The page may have moved, or it may belong to another version or translation.</p>

            <?php if (!isset($extension)): // TODO search currently does not work for extensions ?>
                <div class="guide-error-page__search">
                    <?= SearchForm::widget([
                        'type' => 'guide',
                        'placeholder' => 'Search the guide…',
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="guide-error-page__recovery">

            <?php if (isset($section)):

                /** @var GuideSection[] $alternatives */
                if (isset($extension)) {
                    $versionOptions = Guide::getExtensionOptions($extension);
                    $alternativeGuide = Guide::loadExtension($extension, key($versionOptions), reset($versionOptions)[0]);
                    $alternatives = $alternativeGuide === null ? [] : $alternativeGuide->findSectionInOtherLanguages($section);
                } else {
                    $alternatives = (new Guide('2.0', 'en'))->findSectionInOtherLanguages($section);
                }
                if (!empty($alternatives)): ?>
                    <section class="guide-error-page__section">
                    <h2>This page exists elsewhere</h2>
                    <p>Open it in another available version or language.</p>
                    <ul class="guide-error-page__versions">
                    <?php foreach($alternatives as $oversion => $altSections) {
                        $links = [];
                        echo '<li><strong>Version ' . Html::encode($oversion) . '</strong><div>';
                        foreach($altSections as $altSection) {
                            if (isset($extensionName, $extensionVendor)) {
                                $url = ['guide/extension-view', 'section' => $altSection->name, 'version' => $altSection->guide->version, 'language' => $altSection->guide->language, 'name' => $extensionName, 'vendorName' => $extensionVendor];
                            } else {
                                $url = ['guide/view', 'section' => $altSection->name, 'version' => $altSection->guide->version, 'language' => $altSection->guide->language, 'type' => $altSection->guide->typeUrlName];
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
            <?php endif; ?>

            <section class="guide-error-page__section">
            <h2>Choose another guide</h2>
            <p>Browse every available version and translation.</p>
            <ul class="guide-error-page__versions">
            <?php if (!isset($extension)): ?>
                <li>
                    <strong>Version 3.0</strong>
                    <div><?= Html::a('English', 'https://yiisoft.github.io/docs/guide/', ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?></div>
                </li>
            <?php endif; ?>
            <?php
                if (isset($extension)) {
                    $versionOptions = Guide::getExtensionOptions($extension);
                } else {
                    $guide = new Guide('2.0', 'en');
                    $versionOptions = [];
                    foreach($guide->getVersionOptions() as $oversion) {
                        $versionGuide = new Guide($oversion, 'en');
                        $versionOptions[$oversion] = array_keys($versionGuide->getLanguageOptions());
                    }
                }
                krsort($versionOptions, SORT_NATURAL);

                foreach($versionOptions as $oversion => $languages) {
                    echo '<li><strong>Version ' . Html::encode($oversion) . '</strong><div>';

                    $links = [];
                    foreach($languages as $olanguage) {
                        $languageName = Locale::getDisplayLanguage($olanguage, $olanguage);
                        if (isset($extension)) {
                            $url = ['guide/extension-index', 'version' => $oversion, 'language' => $olanguage, 'name' => $extensionName, 'vendorName' => $extensionVendor];
                        } else {
                            $url = ['guide/index', 'version' => $oversion, 'language' => $olanguage, 'type' => $versionGuide->getTypeUrlName()];
                        }
                        if ($olanguage === 'en') {
                            $links[$olanguage] = '<strong>' . Html::a($languageName, $url) . '</strong>';
                        } else {
                            $links[$olanguage] = Html::a($languageName, $url);
                        }
                    }
                    ksort($links);
                    echo implode(', ', $links);
                    echo '</div></li>';
                }
            ?>
            </ul>
            </section>
    </div>
</main>
