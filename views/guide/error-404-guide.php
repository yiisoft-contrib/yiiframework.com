<?php
/**
 * @var $this yii\web\View
 * @var $section string
 * @var $version string
 * @var $language string
 */

use app\widgets\SearchForm;
use yii\helpers\Html;

$also = '';
$this->title = 'Not Found (#404)';
?>
<?= $this->render('//site/partials/common/_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="site-error content">


        <div class="alert alert-warning">
            <p><strong>Sorry, we could not find this page in the guide.</strong></p>

            <?php if (isset($section)):

                /** @var \app\models\GuideSection[] $alternatives */
                $alternatives = (new \app\models\Guide('2.0', 'en'))->findSectionInOtherLanguages($section);
                if (!empty($alternatives)): ?>

                    <p>A page with this name exists in the following languages and versions:</p>

                    <ul>
                    <?php foreach($alternatives as $version => $altSections) {
                        echo "<li>Version $version:<br>";
                        foreach($altSections as $altSection) {
                            $url = ['guide/view', 'section' => $altSection->name, 'version' => $altSection->guide->version, 'language' => $altSection->guide->language, 'type' => $altSection->guide->typeUrlName];
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
                    $also = ' also ';
                    ?>
                    </ul>

                <?php endif; ?>
            <?php endif; ?>

            <p>The guide is available in the following languages and versions:</p>
            <ul>
            <?php
                $guide = new \app\models\Guide('2.0', 'en');

                foreach($guide->getVersionOptions() as $version) {
                    echo "<li>Version $version:<br>";
                    $versionGuide = new \app\models\Guide($version, 'en');

                    $links = [];
                    foreach($versionGuide->getLanguageOptions() as $language => $languageName) {
                        $url = ['guide/index', 'version' => $versionGuide->version, 'language' => $language, 'type' => $versionGuide->typeUrlName];
                        if ($language === 'en') {
                            $links[$language] = '<strong>' . Html::a($languageName, $url) . '</strong>';
                        } else {
                            $links[$language] = Html::a($languageName, $url);
                        }
                    }
                    ksort($links);
                    echo implode(', ', $links);
                    echo "</li>";
                }
            ?>
            </ul>


            <p>You may <?= $also ?> try searching for a guide page:</p>

            <?= SearchForm::widget([
                'type' => 'guide',
                'placeholder' => 'Search the Guide…',
            ]) ?>


        </div>

    </div>
</div>
