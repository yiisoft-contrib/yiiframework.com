<?php

namespace app\controllers;

use app\models\Badge;
use app\models\Extension;
use app\models\UserBadge;
use app\models\Wiki;
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['profile'],
                'rules' => [
                    [
                        // allow all to a access index and view action
                        'allow' => true,
                        'actions' => ['profile'],
                        'roles' => ['@'],
                    ],
                ]
            ],
        ];
    }

    /**
     * Lists all User models.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['rank' => SORT_ASC],
                'attributes' => [
                    'rank'=> [
                        'asc'=>['rank' => SORT_ASC],
                        'desc'=>['rank' => SORT_DESC],
                        'label'=>'Rank',
                    ],
                    'display_name'=> [
                        'asc'=>['display_name' => SORT_ASC],
                        'desc'=>['display_name' => SORT_DESC],
                        'label'=>'User',
                    ],
                    'joined'=> [
                        'asc'=>['created_at' => SORT_ASC],
                        'desc'=>['created_at' => SORT_DESC],
                        'label'=>'Member Since',
                        'default'=>SORT_DESC,
                    ],
                    'rating'=> [
                        'asc'=>['rating' => SORT_ASC],
                        'desc'=>['rating' => SORT_DESC],
                        'label'=>'Overall Rating',
                        'default'=>SORT_DESC,
                    ],
                    'extensions'=> [
                        'asc'=>['extension_count' => SORT_ASC],
                        'desc'=>['extension_count' => SORT_DESC],
                        'label'=>'Extensions',
                        'default'=>SORT_DESC,
                    ],
                    'wiki'=> [
                        'asc'=>['wiki_count' => SORT_ASC],
                        'desc'=>['wiki_count' => SORT_DESC],
                        'label'=>'Wiki Articles',
                        'default'=>SORT_DESC,
                    ],
                    'comments'=> [
                        'asc'=>['comment_count' => SORT_ASC],
                        'desc'=>['comment_count' => SORT_DESC],
                        'label'=>'Comments',
                        'default'=>SORT_DESC,
                    ],
                    'posts'=> [
                        'asc'=>['post_count' => SORT_ASC],
                        'desc'=>['post_count' => SORT_DESC],
                        'label'=>'Forum Posts',
                        'default'=>SORT_DESC,
                    ],
                ],
            ]
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'userCount' => User::find()->count(),
            'wikis' => $model->getWikis()->orderBy('title')->active()->all() ,
            'extensions' => $model->getExtensions()->orderBy('name')->active()->all(),
        ]);
    }

    public function actionProfile()
    {
        $userId = Yii::$app->user->getId();

        $extensions = Extension::find()
            ->active()
            ->where(['owner_id' => $userId])
            ->orderBy('created_at DESC')
            ->all();

        $wikiPages = Wiki::find()
            ->active()
            ->where(['creator_id' => $userId])
            ->orderBy('created_at DESC')
            ->all();

        return $this->render('profile', [
            'model' => Yii::$app->user->identity,
            'extensions' => $extensions,
            'wikiPages' => $wikiPages,
        ]);
    }

    public function actionHalloffame()
   	{
        return $this->render('halloffame');
   	}

    public function actionBadges()
    {
        $badges = Badge::find()->orderBy('achieved DESC, urlname')->all();
        return $this->render('badges', [
            'badges' => $badges,
            'counts' => UserBadge::countUsers(),
        ]);
    }

    public function actionViewBadge($name)
    {
        $badge = Badge::find()->where(['urlname' => $name])->one();
        if ($badge === null) {
            throw new NotFoundHttpException('Unknown badge');
        }
        return $this->render('badge', [
            'badge' => $badge,
            'users' => UserBadge::listUsers($badge),
            'count' => UserBadge::countUsers($badge),
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
