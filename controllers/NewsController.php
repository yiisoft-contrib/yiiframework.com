<?php

namespace app\controllers;

use app\models\NewsTag;
use Yii;
use app\models\News;
use app\models\NewsSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
{
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
				        'actions' => ['index', 'view'],
			        ],
			        [
				        // allow all to a access index and view action
				        'allow' => true,
				        'actions' => ['admin', 'create', 'update', 'list-tags'],
				        'roles' => ['news:pAdmin'],
			        ],
		        ]
	        ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionIndex($year = null, $tag = null)
    {
        $query = News::find()->latest();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // also show unublished news if user is not admin
        if (Yii::$app->user->can('news:pAdmin')) {
            $query->orWhere(['!=', 'status', News::STATUS_PUBLISHED]);
        }

        if ($year !== null) {
            $year = (int) $year;
            if ($year < 1900) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $query->andWhere('YEAR(news_date) = :year', [':year' => $year]);
        }

        if ($tag !== null) {
            $tag = NewsTag::find()->where(['slug' => $tag])->one();
            if ($tag === null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            $query->joinWith('tags AS tags');
            $query->andWhere(['tags.id' => $tag->id]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'year' => $year,
            'tag' => $tag,
        ]);
    }

    /**
     * Lists all News models.
     * @return mixed
     */
    public function actionAdmin()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
	    $model = $this->findModel($id);

        if (!Yii::$app->user->can('news:pAdmin') && $model->status != News::STATUS_PUBLISHED) {
   		    throw new NotFoundHttpException('The requested page does not exist.');
   	    }

        // normalize slug URL
        $slug = Yii::$app->request->get('name');
        if ($model->slug !== $slug) {
            return $this->redirect(['news/view', 'id' => $model->id, 'name' => $model->slug], 301);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * actionList to return matched tags
     */
    public function actionListTags($query)
    {
        $models = NewsTag::find()->where(['like', 'name', $query])->all();
        $items = [];

        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $items;
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
