<?php

namespace app\controllers;

use app\models\Star;
use app\models\Extension;
use app\models\ExtensionCategory;
use app\models\ExtensionTag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ExtensionController extends Controller
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
                        'allow' => true,
                        'actions' => ['create', 'list-tags', 'update', 'keep-alive'],
                        'roles' => ['@'],
                    ],
//                    [
//                        // allow all to a access index and view action
//                        'allow' => true,
//                        'actions' => ['admin', 'create', 'update', 'delete', 'list-tags'],
//                        'roles' => ['news:pAdmin'],
//                    ],
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

    public function actionIndex($category = null, $tag = null)
    {
        $query = Extension::find()->active()->with(['owner', 'category']);

        if ($category !== null) {
            $category = (int) $category;
            if (ExtensionCategory::findOne($category) === null) {
                throw new NotFoundHttpException('The requested category does not exist.');
            }
            $query->andWhere(['category_id' => $category]);
        }

        $tagModel = null;
        if ($tag !== null) {
            $tagModel = ExtensionTag::findOne(['slug' => $tag]);
            if ($tagModel === null) {
                throw new NotFoundHttpException('The requested tag does not exist.');
            }
            $query->joinWith('tags', false);
            $query->andWhere(['extension_tag_id' => $tagModel->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes'=> [
                    'create'=> [
                        'asc'=>['created_at' => SORT_ASC],
                        'desc'=>['created_at' => SORT_DESC],
                        'label'=>'Sorted by date',
                        'default'=>'desc',
                    ],
                    'update'=> [
                        'asc'=>['updated_at' => SORT_ASC],
                        'desc'=>['updated_at' => SORT_DESC],
                        'label'=>'Sorted by date (updated)',
                        'default'=>'desc',
                    ],
                    'rating'=> [
                        'asc'=>['rating' => SORT_ASC],
                        'desc'=>['rating' => SORT_DESC],
                        'label'=>'Sorted by rating',
                        'default'=>'desc',
                    ],
                    'comments'=> [
                        'asc'=>['comment_count' => SORT_ASC],
                        'desc'=>['comment_count' => SORT_DESC],
                        'label'=>'Sorted by comments',
                        'default'=>'desc',
                    ],
                    'downloads'=> [
                        'asc'=>['download_count' => SORT_ASC],
                        'desc'=>['download_count' => SORT_DESC],
                        'label'=>'Sorted by downloads',
                        'default'=>'desc',
                    ],
                ],
                'defaultOrder'=> ['create'=>SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 154,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tag' => $tagModel,
            'category' => $category,
        ]);
    }

    public function actionView($name)
    {
        $model = $this->findModel($name);

        // normalize URL, redirect non-case sensitive URLs
        if ($model->name !== $name) {
            return $this->redirect(['view', 'name' => $model->name], 301);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        // TODO permission
//        if(!user()->dbUser->canCreateExtension())
//            throw new CHttpException(403,'Sorry, you are too new to write a extension article. Please try posting it in our forum first.');

        $model = new Extension();
        $model->initDefaults();
        $post = Yii::$app->request->post('Extension', []);
        if (isset($post['from_packagist'])) {

            $model->from_packagist = $post['from_packagist'];
            $model->scenario = 'create_' . ($model->from_packagist == 1 ? 'packagist' : 'custom');

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                if ($model->from_packagist) {
                    // TODO import data
                    $model->name = md5(time());

                    $model->save(false);
                } else {
                    $model->save(false);
                }


                Star::castStar($model, Yii::$app->user->id, 1);
                return $this->redirect(['view', 'name' => $model->name]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($name)
    {
        // TODO permission
        $model = $this->findModel($name);
        $model->scenario = 'update_' . ($model->from_packagist == 1 ? 'packagist' : 'custom');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // TODO notification email for followers
//            if(($changes=$model->findChanges($oldAttributes))!='')
//                $model->notifyFollowers($changes);

            Star::castStar($model, Yii::$app->user->id, 1);
            return $this->redirect(['view', 'name' => $model->name]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * actionList to return matched tags
     */
    public function actionListTags($query)
    {
        $models = ExtensionTag::find()->where(['like', 'name', $query])->all();
        $items = [];

        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $items;
    }


    public function actionHistory($id)
    {
        $model = $this->findModel($id);

        return $this->render('history', [
            'model'=>$model,
            'dataProvider' => new ActiveDataProvider([
                'query' => $model->getRevisions(),
                'pagination' => false,
                'sort' => false, /*[
                    'defaultOrder' => ['updated_at' => SORT_DESC],
                ]*/
            ])
        ]);
    }

    /**
     * Just reply with a 'pong' to the session keep alive call.
     * This method is only accessable for logged in users, so session will be opened.
     * @return string
     */
    public function actionKeepAlive()
    {
        return 'pong';
    }

    /**
     * Finds the Extension model based on its name.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name
     * @return Extension the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        if (($model = Extension::find()->where(['name' => $name])->active()->one()) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
