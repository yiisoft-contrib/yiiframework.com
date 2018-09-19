<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'User: ' . Html::encode($model->username);
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('View Profile', ['user/view', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
    <?= Html::a("View Comments ({$model->comment_count})", ['comment-admin/index', 'CommentSearch[user.username]' => $model->username], ['class' => 'btn btn-default']) ?>
    <?= Html::a("View Wikis ({$model->wiki_count})", ['wiki-admin/index', 'WikiSearch[creator.username]' => $model->username], ['class' => 'btn btn-default']) ?>
    <?= Html::a("View Extensions ({$model->extension_count})", '#', ['class' => 'btn btn-default disabled']) ?>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    <?= Html::a('Unpublish content', ['unpublish-content', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to unpublish ALL content added by this user?',
            'method' => 'post',
        ],
    ]) ?>
</p>
<?php

$authClients = [];
foreach($model->authClients as $authClient) {
    if ($authClient->source === 'github') {
        $authClients[] = Html::a(Html::encode($authClient->source), 'https://github.com/' . $authClient->source_login, ['target' => '_blank', 'rel' => 'noopener']) . ' '
            . Html::encode("({$authClient->source_login}, {$authClient->source_email})");
    } else {
        $authClients[] = Html::encode($authClient->source);
    }
}
$authClients = empty($authClients) ? '<span class="not-set">(none)</span>' : implode(', ', $authClients);

?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'username',
        'passwordType',
        [
            'label' => 'Auth Clients',
            'value' => $authClients,
            'format' => 'raw',
        ],
        'login_time:datetimerel',
        'login_attempts',
        'login_ip',
        'email:email',
        'email_verified:boolean',
        [
            'attribute' => 'status',
            'value' => $model->statusLabel,
            'format' => 'raw',
        ],
        'created_at:datetimerel',
        'updated_at:datetimerel',
    ],
]) ?>

<h2>Forum integration</h2>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'forum_id',
        [
            'label' => 'Forum Profile',
            'value' => Yii::getAlias($model->getForumUrl()),
            'format' => 'url',
        ],
    ],
]) ?>
