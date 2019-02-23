<?php

/** @var array $archiveEntries */

if (empty($archiveEntries)) {
    return;
}

?>
<h2>News Archive</h2>

<ul>
<?php
    $archive = Yii::$app->request->get('year');
    foreach($archiveEntries as $date => $entry) {
        if ($date == $archive) {
            echo '<li class="active">';
        } else {
            echo '<li>';
        }
        echo $entry . '</li>';
    } ?>
</ul>
