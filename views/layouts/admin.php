<?php

/** @var $this \yii\web\View */
/** @var $content string */

$this->beginContent('@app/views/layouts/main.php');
?>
<div class="container style_external_links">

    <?php

    if (isset($this->params['breadcrumbs'])) {
        echo \yii\widgets\Breadcrumbs::widget([
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
