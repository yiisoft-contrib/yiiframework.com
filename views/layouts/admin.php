<?php

/** @var $this View */
/** @var $content string */

use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->beginContent('@app/views/layouts/main.php');
?>
<div class="container style_external_links">

    <?php

    if (isset($this->params['breadcrumbs'])) {
        echo Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs'],
            'homeLink' => [
                'label' => 'Admin',
                'url' => ['admin/index'],
            ],
        ]);
    }

    ?>

    <?= $content ?>

</div>

<?php $this->endContent(); ?>
