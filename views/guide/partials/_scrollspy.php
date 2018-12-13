<?php
/**
 * @var $this yii\web\View
 * @var $guide app\models\Guide
 * @var $section app\models\GuideSection
 * @var $notes bool whether to show "user contributed notes" link
 */

use yii\helpers\Html;

?>
<div class="visible-huge fixed lang-<?= (!empty($missingTranslation)) ? 'en' : $guide->language ?>">
    <nav id="scrollnav" data-spy="affix" data-offset-top="120">
        <ul class="nav hidden-xs hidden-sm">
            <?php
                echo '<li>' . Html::a($section->getTitle(), '#' . (isset($section->headings['id']) ? $section->headings['id'] : '')) . '</li>';
                $sections = isset($section->headings['sections']) ? $section->headings['sections'] : [];
                foreach($sections as $heading) {
                    echo '<li>' . Html::a(Html::encode(strip_tags($heading['title'])), '#' . $heading['id']);
                    if (isset($heading['sub'])) {
                        echo '<ul class="nav">';
                        foreach ($heading['sub'] as $subheading) {
                            echo '<li class="subheading">' . Html::a(Html::encode(strip_tags($subheading['title'])), '#' . $subheading['id']) . '</li>';
                        }
                        echo "</ul>";
                    }
                    echo '</li>';
                }
                if (isset($notes) && $notes) {
                    //echo '<li class="separator"></li>';
                    echo '<li>' . Html::a('User Contributed Notes', '#user-notes') . '</li>';
                }
            ?>
        </ul>
    </nav>
</div>
