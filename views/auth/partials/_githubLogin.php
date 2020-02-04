<?php

use yii\bootstrap4\Html;

?>
<div class="col-md-6 social-login">
      <span class="github-icon">
          <i class="fa fa fa-github-square"></i>
      </span>
      <p>Did you sign up with your GitHub Account?</p>
      <?= Html::a('Login with GitHub', ['auth/auth', 'authclient' => 'github'], ['class' => 'btn btn-lg']) ?>
      <p>To connect an existing account with GitHub, log in first.</p>
</div>