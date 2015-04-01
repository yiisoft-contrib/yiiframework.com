<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Report a Security Issue';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container content">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please use the <?php echo Html::a('contact us form', ['site/contact']) ?> to report to us any security issue
        you find in Yii. DO NOT use the issue tracker or discuss it in the public forum as it will cause more damage
        than help.</p>

    <p>Once we receive your issue report, we will treat it as our highest priority. We will generally take the
        following steps in responding to security issues.</p>

    <ol>
        <li>Confirm the issue. We may contact with you for further discussion. We will send you an acknowledgement
            after the issue is confirmed.
        </li>
        <li>Work on a solution.</li>
        <li>Release a patch to all maintained versions.</li>
    </ol>

    <p><?php echo Html::a('Contact us', ['site/contact']) ?> to report a security issue.</p>

</div>
