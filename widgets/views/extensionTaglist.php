<?php

/** @var array $tagEntries */

if (empty($tagEntries)) {
    return;
}

?>
<h2><?= $this->context->extension ? 'Tags' : 'Popular Tags' ?></h2>

<ul>
<?php
    $selectedTag = Yii::$app->request->get('tag');
    foreach($tagEntries as $date => $entry) {
        if ($date === $selectedTag) {
            echo '<li class="active">';
        } else {
            echo '<li>';
        }
        echo $entry . '</li>';
    } ?>
</ul>
