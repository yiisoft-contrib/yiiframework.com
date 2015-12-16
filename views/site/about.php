<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;

// TODO this is duplicate content with the guide
?>
<?= $this->render('_heading.php', ['title' => $this->title]) ?>
<div class="container">
    <div class="row">
        <div class="content">
            <p>Yii is a high-performance modern PHP framework best for developing both web applications and APIs.</p>

            <p>Yii helps developers build complex applications and deliver them on-time.</p>

            <p><strong>Yii</strong> is pronounced as Yee or [ji:], and is an acronym for "<strong>Yes It Is!</strong>".
                This is often the accurate, and most concise response to inquires from those new to Yii:
                Is it fast? ... Is it secure? ... Is it professional? ... Is it right for my next project? ... <strong>Yes, it is!</strong></p>

            <p>Yii is a <?= Html::a('free, open-source', ['site/license']) ?> Web application development framework written in PHP5 that promotes clean, DRY design
                and encourages rapid development. It works to streamline your application development and helps to ensure an
                extremely efficient, extensible, and maintainable end product.</p>

            <p>Being constantly performance optimized, Yii is a perfect choice for any sized project. However, it has been built
                with sophisticated, enterprise applications in mind. You have full control over the configuration from
                head-to-toe (presentation-to-persistence) to conform to your enterprise development guidelines. It comes
                packaged with tools to help test and debug your application, and has clear and comprehensive <?= Html::a('documentation', ['guide/entry']) ?>.</p>

            <h2>History</h2>

            <p>Yii is the brainchild of its founder, Qiang Xue, who started the Yii project on January 1, 2008. Qiang
                previously developed and maintained the Prado framework. The years of experience gained and developer feedback
                gathered from that project solidified the need for an extremely fast, secure and professional framework that is
                tailor-made to meet the expectations of Web 2.0 application development. On December 3, 2008, after nearly one
                year's development, Yii 1.0 was formally released to the public.</p>

            <p>Its extremely impressive performance metrics when compared to other PHP-based frameworks immediately drew very
                positive attention and its popularity and adoption continues to grow at an ever increasing rate.</p>

            <p>On October 2014 Yii 2.0.0 was released which is a complete rewrite over the previous version that was made
               in order to build a state-of-the-art PHP framework by keeping the original simplicity and extensibility of Yii
               while adopting the latest technologies and features to make it even better.</p>
        </div>
    </div>
</div>
