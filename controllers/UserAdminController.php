<?php

namespace app\controllers;

use app\components\UserPermissions;
use app\models\Comment;
use app\models\Extension;
use app\models\Wiki;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserAdminController extends BaseController
{
    public $layout = 'admin';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
   		        'class' => AccessControl::class,
   		        'rules' => [
   			        [
   				        // allow all to a access index and view action
   				        'allow' => true,
   				        'actions' => ['index', 'view', 'update', 'delete', 'unpublish-content'],
                        'roles' => [UserPermissions::PERMISSION_MANAGE_USERS],
   			        ],
   		        ]
   	        ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'unpublish-content' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
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
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Unpublish all content added by this user
     */
    public function actionUnpublishContent($id)
    {
        $user = $this->findModel($id);

        $w = 0;
        foreach ($user->wikis as $wiki) {
            $wiki->status = Wiki::STATUS_DELETED;
            $wiki->save(false);
            $w++;
        }
        $e = 0;
        foreach ($user->extensions as $extension) {
            $extension->status = Extension::STATUS_DELETED;
            $extension->save(false);
            $e++;
        }
        $c = 0;
        foreach (Comment::find()->where(['user_id' => $user->id])->all() as $comment) {
            $comment->status = Comment::STATUS_DELETED;
            $comment->save(false);
            $c++;
        }
        Yii::$app->session->setFlash('success', "Unpublished $w wikis, $e extensions and $c comments.");

        return $this->redirect(['view', 'id' => $id]);
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
