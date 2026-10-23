<?php

use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'id',
            'content' => static function($model) {
                return Html::a(Html::encode($model->id), ['user-admin/view', 'id' => $model->id]);
            },
        ],
        [
            'attribute' => 'username',
            'content' => static function($model) {
                return Html::a(Html::encode($model->username), ['user-admin/view', 'id' => $model->id]);
            },
        ],
        [
            'label' => 'Auth Methods',
            'content' => static function($model) {
                /** $model app\models\User */
                $html = [];
                if ($model->getPasswordType() !== 'NONE') {
                    $html[] = 'PW';
                }
                if (!empty($model->authClients)) {
                    foreach($model->authClients as $authClient) {
                        $html[] = Html::encode($authClient->source);
                    }
                }

                return empty($html) ? '<span class="not-set">(none)</span>' : implode(', ', $html);
            },
        ],

        'email:email',
        [
            'attribute' => 'status',
            'value' => 'statusLabel',
            'format' => 'raw',
            'filter' => \app\models\User::getStatuses(),
        ],
        'created_at:datetime',
        // 'updated_at',

        [
            'class' => ActionColumn::class,
            'contentOptions' => ['class' => 'action-column'],
        ],
    ],
]) ?>
