<?php

use yii\helpers\Html;

?>
<div class="col-md-3 social-login">
      <span class="github-icon">
          <i class="fa fa-github"></i>
      </span>
      <h4>Did you sign up with your<br/>Github Account?</h4>
      <?= Html::a('Login with Github', ['auth/auth', 'authclient' => 'github'], ['class' => 'btn btn-lg']) ?>
      <h4>To connect your existing account<br/>with Github, log in first.</h4>
</div>
