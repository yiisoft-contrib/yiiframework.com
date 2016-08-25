<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
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
        return $this->render('view', [
            'model' => $this->findModel($id),
            'userCount' => User::find()->count(),
        ]);
    }

    public function actionHalloffame()
   	{
        return $this->render('halloffame');
   	}

    public function actionBadges()
    {
        // TODO implement (migrate from old site)
        echo 'TODO implement (migrate from old site)';
    }

    public function actionViewBadge($name)
    {
        // TODO implement (migrate from old site)
        echo 'TODO implement (migrate from old site)';
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
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
