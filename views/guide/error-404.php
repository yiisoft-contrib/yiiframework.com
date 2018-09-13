<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var Doc $doc
 */

use app\models\Doc;
use app\widgets\SearchForm;
use app\widgets\SideNav;
use yii\helpers\Html;

$this->beginContent('@app/views/guide/partials/_guide-layout.php', [
    'guide' => $guide,
]);
$this->title = 'Not Found (#404)';
?>
    <?= $this->render('//site/partials/common/_heading.php', ['title' => $this->title]) ?>
    <div class="content">

        <div class="alert alert-warning">
            <p><strong>Sorry, we could not find this page in the guide.</strong></p>

            <?php

            /** @var \app\models\GuideSection[] $alternatives */
            $alternatives = $guide->findSectionInOtherLanguages($section->name);
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
                ?>
                </ul>

                <p>You may also try searching for a guide page:</p>
            <?php else: ?>
                <p>You may try searching for a guide page:</p>
            <?php endif; ?>

            <?= SearchForm::widget([
                'type' => 'guide',
                'placeholder' => 'Search the Guide…',
            ]) ?>

        </div>
    </div>

<?php $this->endContent() ?>