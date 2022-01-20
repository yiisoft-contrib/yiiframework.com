<?php
/**
 * @var $this yii\web\View
 * @var $section string
 * @var $version string
 * @var $language string
 */

use app\models\Guide;
use app\widgets\SearchForm;
use yii\helpers\Html;

$this->title = 'Not Found (#404)';
?>
<?= $this->render('//guide/partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="site-error content">


        <div class="alert alert-warning">
            <p><strong>Sorry, we could not find this page in the guide.</strong></p>

            <?php if (isset($section)):

                /** @var \app\models\GuideSection[] $alternatives */
                if (isset($extension)) {
                    $versionOptions = Guide::getExtensionOptions($extension);
                    $alternativeGuide = Guide::loadExtension($extension, key($versionOptions), reset($versionOptions)[0]);
                    $alternatives = $alternativeGuide === null ? [] : $alternativeGuide->findSectionInOtherLanguages($section);
                } else {
                    $alternatives = (new Guide('2.0', 'en'))->findSectionInOtherLanguages($section);
                }
                if (!empty($alternatives)): ?>

                    <p>A page with this name exists in the following languages and versions:</p>

                    <ul>
                    <?php foreach($alternatives as $oversion => $altSections) {
                        echo "<li>Version $oversion:<br>";
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
                        echo "</li>";
                    }
                    ?>
                    </ul>

                <?php endif; ?>
            <?php endif; ?>

            <p>The guide is available in the following languages and versions:</p>
            <ul>
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
                    echo "<li>Version $oversion:<br>";

                    $links = [];
                    foreach($languages as $olanguage) {
                        $languageName = \Locale::getDisplayLanguage($olanguage, $olanguage);
                        if (isset($extension)) {
                            $url = ['guide/extension-index', 'version' => $oversion, 'language' => $olanguage, 'name' => $extensionName, 'vendorName' => $extensionVendor];
                        } else {
                            $url = ['guide/index', 'version' => $oversion, 'language' => $olanguage, 'type' => $versionGuide->typeUrlName];
                        }
                        if ($olanguage === 'en') {
                            $links[$olanguage] = '<strong>' . Html::a($languageName, $url) . '</strong>';
                        } else {
                            $links[$olanguage] = Html::a($languageName, $url);
                        }
                    }
                    ksort($links);
                    echo implode(', ', $links);
                    echo "</li>";
                }
            ?>
            </ul>

            <?php if (!isset($extension)): // TODO search currently does not work for extensions
             ?>

            <p>You may also try searching for a guide page:</p>

            <?= SearchForm::widget([
                'type' => 'guide',
                'placeholder' => 'Search the Guide…',
            ]) ?>

            <?php endif; ?>

        </div>

    </div>
</div>
