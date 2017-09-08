<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Report a Security Issue';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container site-header">
    <div class="row">
        <div class="col-md-6">
            <h1 class="security">Report a<br>Security Issue</h1>
            <h2>Let's make Yii better</h2>
        </div>
        <div class="col-md-6">
            <img class="background" src="<?= Yii::getAlias('@web/image/issues/issues_secret.svg')?>" alt="">
        </div>
    </div>
</div>

<div class="container report">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <p>Please use the security issue form to report to us any security issue
                    you find in Yii. DO NOT use the issue tracker or discuss it in the public forum as it will cause more damage
                    than help.</p>
            

                <div class="heading-separator">
                    <h2><span>Security Issue Form</span></h2>
                </div>
            </div>

            <div class="col-md-9">
                <form>
                    <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="">
                  </div>

                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="">
                  </div>
                  
                  <div class="form-group">
                    <label for="issue">Issue</label>
                    <textarea class="form-control" id="issue" rows="10"></textarea>
                  </div>
                  <button type="submit" class="btn btn-lg btn-primary btn-send">Send</button>
                </form>
            </div>
            <div class="col-md-3">
                <p class="small">Once we receive your issue report, we will treat it as our highest priority. We will generally take the
                    following steps in responding to security issues.</p>

                <ol class="issue-process">
                    <li>Confirm the issue. We may contact with you for further discussion. We will send you an acknowledgement
                        after the issue is confirmed.
                    </li>
                    <li>Work on a solution.</li>
                    <li>Release a patch to all maintained versions.</li>
                </ol>
            </div>
        </div>
    </div>
</div>