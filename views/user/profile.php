<?php

use app\models\Badge;
use app\models\Star;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $extensions \app\models\Extension[] */
/* @var $wikiPages \app\models\Wiki[] */

$this->title = 'Hi, ' . $model->username . '!';

echo $this->render('//site/partials/common/_admin_heading.php', [
    'title' => $this->title,
    'menu' => [
        ['label' => 'User Admin', 'url' => ['user-admin/index'], 'visible' => Yii::$app->user->can('users:pAdmin') ],
        ['label' => 'Update User', 'url' => ['user-admin/view', 'id' => $model->id], 'visible' => Yii::$app->user->can('users:pAdmin') ],
    ]
]);

?>
<div class="container style_external_links">
    <div class="content">

        <?= \app\widgets\Alert::widget() ?>

        <div class="row">
            <div class="col-md-4">
                <h2>Your Profile</h2>
                <p>This is your private profile settings page.</p>

                <ul>
                    <li><?= Html::a('&raquo; View forum profile', $model->forumUrl); ?></li>
                    <li><?= Html::a('&raquo; View public profile', ['view', 'id' => $model->id]); ?></li>
                </ul>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'username',
                        'created_at:datetime',

                    ],
                ]) ?>
            </div>
            <div class="col-md-4">
                <h2>Authentification</h2>

                <h3>OAuth login</h3>
                <p>
                <?php

                if (empty($model->authClients)) {
                    echo 'Your account is not connected with your Github profile. ';
                    echo 'Click ' . Html::a('here', ['auth/connect-auth', 'source' => 'github'], ['data-method' => 'post']) . ' to connect them. ';
                } else {
                    foreach($model->authClients as $client) {
                        echo 'Your account is connected with your ' . ucfirst($client->source) . ' profile: '
                            . Html::a('http://github.com/' . Html::encode($client->source_login), 'http://github.com/' . $client->source_login) . '. ';

                        if ($model->passwordType === 'NONE') {
                            echo 'To remove the connection, you should enable Password login first.';
                        } else {
                            echo Html::a('Remove connection', ['/auth/remove-auth', 'source' => $client->source], [
                                'data-confirm' => 'Are you sure you want to remove the connection to your ' . ucfirst($client->source) . ' profile?',
                                'data-method' => 'post',
                            ]);
                        }
                    }
                    echo '</p><p>You can log in using that account without the need for defining a password for the Yii website.';
                }

                ?>
                </p>

                <h3>Password login</h3>
                <p>
                    <?php switch($model->passwordType)
                    {
                        case 'LEGACYMD5':
                        case 'LEGACYSHA':
                            echo '<strong>Your password is stored in the database using a deprecated hasing algorithm. Please log out and log in again to fix this.</strong></p><p>';
                            // no break
                        case 'NEW':
                            echo 'Password login is enabled. That means that you can log in with your username and password.';

                            if (empty($model->authClients)) {
                                echo '</p><p>To disable Password login, you need to enable OAuth login first.';
                            } else {
                                echo '</p><p>If you want to disable Password login, to use OAuth login only, click ' . Html::a('here', ['auth/disable-password'], [
                                        'data-confirm' => 'Are you sure you want to disable password login?',
                                        'data-method' => 'post'
                                    ]) . '.';
                            }
                            break;
                        case 'NONE':
                            echo 'Password login is disabled. To enable it, you can request a new password ' . Html::a('here', ['auth/request-password-reset']) . '.';
                    }

                    ?>
                </p>
            </div>
            <div class="col-md-4">
                    <h2>Your Extensions (<?= Html::encode($model->extension_count) ?>) </h2>

                    <ul>
                    <?php foreach ($extensions as $extension): ?>
                        <li><?= Html::a(Html::encode($extension->getLinkTitle()), $extension->getUrl())?></li>
                    <?php endforeach ?>
                    </ul>

                    <h2>Your Wiki entries (<?= Html::encode($model->wiki_count) ?>)</h2>

                    <ul>
                    <?php foreach ($wikiPages as $wikiPage): ?>
                        <li><?= Html::a(Html::encode($wikiPage->getLinkTitle()), $wikiPage->getUrl()) ?></li>
                    <?php endforeach ?>
                    </ul>

                    <h2>Your Stars (following)</h2>

                    <?php
                        $targets = Star::getTargets($model->id);
                    ?>
                    <ul class="g-list-none">
                        <?php foreach($targets as $target): ?>
                        <li>
                            <?php switch(get_class($target))
                            {
                                case \app\models\Wiki::class:
                                    echo "[Wiki] " . Html::a(Html::encode($target->title), ['wiki/view', 'id' => $target->id, 'name' => $target->slug]);
//                                case \app\models\Extension::class:
//                                    echo "[Extension] " . Html::a(Html::a($target->title), ['extension/view', 'id' => $model->id, 'name' => $model->slug]);
                            } ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>


            </div>
        </div>




    </div>
</div>