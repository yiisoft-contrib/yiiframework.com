<?php

use app\components\objectKey\ObjectKeyInterface;
use app\models\Linkable;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $extensions \app\models\Extension[] */
/* @var $wikiPages \app\models\Wiki[] */
/* @var Linkable[]|ObjectKeyInterface[] $starTargets */

$this->title = 'Hi, ' . $model->username . '!';

?>
<div class="container style_external_links">
    <div class="content">

        <?= \app\widgets\Alert::widget() ?>

        <div class="row">
            <div class="col-xs-12">
                <div class="heading-separator">
                    <h2><span>Your Profile</span></h2>
                </div>

                <ul>
                    <?php if ($model->email): ?>
                        <li>
                            Your email is <?= Html::encode($model->email) ?>.
                            <?php if ($model->email_verified): ?>
                                It is verified.
                            <?php else: ?>
                                It is not verified yet. <?= Html::a('Verify', ['user/request-email-verification']) ?>.
                            <?php endif ?>
                        </li>
                    <?php endif ?>
                    <li><?= Html::a('View forum profile', $model->forumUrl); ?></li>
                    <li><?= Html::a('View public profile', ['view', 'id' => $model->id]); ?></li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 heading-separator">
                <h2><span>Authentication</span></h2>
            </div>
            <div class="col-xs-12 col-md-6">
                <h3>OAuth login</h3>
                <p>
                    <?php

                    if (empty($model->authClients)) {
                        echo 'Your account is not connected with Github yet. ';
                        echo Html::a('Click here to connect', ['auth/connect-auth', 'source' => 'github'], ['data-method' => 'post']) . '.';
                    } else {
                        foreach ($model->authClients as $client) {
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
            </div>
            <div class="col-xs-12 col-md-6">
                <h3>Password login</h3>
                <p>
                    <?php switch ($model->passwordType) {
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
        </div>
        <?php /*
            <div class="col-md-4">
                <h3>Email addresses</h3>

                <p>
                    TODO
                </p>
            </div>
            */ ?>
        <div class="col-xs-12 heading-separator">
            <h2><span>Content</span></h2>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <h2>Your Extensions (<?= Html::encode($model->extension_count) ?>) </h2>

                <?php if (empty($extensions)): ?>
                    <p>
                        You have not published any extension yet.
                        If you have created an extension, you may <?= Html::a('add it here', ['extension/create']) ?>.
                    </p>
                <?php else: ?>

                    <ul>
                        <?php foreach ($extensions as $extension): ?>
                            <li><?= Html::a(Html::encode($extension->getLinkTitle()), $extension->getUrl()) ?></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="col-xs-12 col-md-6">
                <h2>Your Wiki articles (<?= Html::encode($model->wiki_count) ?>)</h2>

                <?php if (empty($wikiPages)): ?>
                    <p>
                        You have not created any wiki articles yet.
                        Wiki articles are extended documentation references about a Yii related topic.
                        If you have an idea for a new article, you may <?= Html::a('create one now', ['wiki/create']) ?>
                        .
                    </p>
                <?php else: ?>

                    <ul>
                        <?php foreach ($wikiPages as $wikiPage): ?>
                            <li><?= Html::a(Html::encode($wikiPage->getLinkTitle()), $wikiPage->getUrl()) ?></li>
                        <?php endforeach ?>
                    </ul>

                <?php endif; ?>
            </div>

            <div class="col-xs-12 col-md-12">
                <h2>Your Stars (following)</h2>

                <?php if (empty($starTargets)): ?>
                    <p>
                        You are currently not following any items.
                        Click on the star icon on Wikis and Extensions to start following them to receive update
                        notifications.
                    </p>
                <?php else: ?>
                    <p>
                        You may click on a star to stop following an item.
                        That means you will no longer be notified about changes for it.
                    </p>

                    <ul class="profile-star-list">
                        <?php foreach ($starTargets as $target): ?>
                            <li>
                                <?= \app\widgets\Star::widget([
                                    'model' => $target,
                                    'starValue' => 1,
                                ]) ?>
                                <?= '[' . $target->getObjectType() . '] ' . Html::a(Html::encode($target->getLinkTitle()), $target->getUrl()); ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
