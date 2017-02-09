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

    public function actionView($name) // TODO find by name
    {
        $model = $this->findModel($id);

        // normalize slug URL
        $slug = Yii::$app->request->get('name');
        if ($model->slug !== $slug) {
            return $this->redirect(['extension/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $revision], 301);
        }

        // update view count
        if (Yii::$app->request->isGet) {
            $model->updateCounters(['view_count' => 1]);
        }

        return $this->render('view', [
            'model' => $model,
            'revision' => $revision !== null ? $this->findRevision($id, $revision) : null,
        ]);
    }

    public function actionCreate()
    {
        // TODO permission
//        if(!user()->dbUser->canCreateExtension())
//            throw new CHttpException(403,'Sorry, you are too new to write a extension article. Please try posting it in our forum first.');

        $model = new Extension();
        $model->scenario = 'create';

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {

            Star::castStar($model, Yii::$app->user->id, 1);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create',array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id, $revision = null)
    {
        // TODO permission
//        if(!user()->dbUser->canCreateExtension())
//            throw new CHttpException(403,'Sorry, you are too new to write a extension article. Please try posting it in our forum first.');

        $model = $this->findModel($id, $revision);
        $model->scenario = 'update';

        if ($revision !== null) {
            $rev = $this->findRevision($id, $revision);
            $model->memo='Reverted to revision #' . $rev->revision;
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {

            // TODO notification email for followers
//            if(($changes=$model->findChanges($oldAttributes))!='')
//                $model->notifyFollowers($changes);

            Star::castStar($model, Yii::$app->user->id, 1);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update',array(
            'model' => $model,
        ));
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
     * Display the Diff of one revision (r1 is set) or the diff between two revisions (r1 and r2).
     *
     * Optionally the revision ids can be passed as array r (used by gridview select.
     */
    public function actionRevision($id, $r1 = null, $r2 = null, array $r = [])
    {
        // if input revisions are given as array
        if (is_array($r) && count($r) == 2) {
            asort($r);
            list($r1, $r2) = array_values($r);
        }

        $left = ExtensionRevision::findOne(['extension_id' => $id, 'revision' => $r1]);
        if ($left === null) {
            throw new NotFoundHttpException('The requested revision does not exist.');
        }
        $model = $left->extension;
        if ($model->status !== Extension::STATUS_PUBLISHED) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($r2 !== null) {
            if ($r2 === 'latest') {
                $right = ExtensionRevision::findLatest($id);
            } else {
                $right = ExtensionRevision::findOne(['extension_id' => $id, 'revision' => $r2]);
            }
            if ($right === null) {
                throw new NotFoundHttpException('The requested revision does not exist.');
            }
            $diffSingle = null;
        } else {
            $right = $left;
            $left = $right->findPrevious();
            $diffSingle = $right;
        }
        if ($left === null) {
            return $this->redirect(['view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $right->revision]);
        }

        return $this->render('revision', [
            'model' => $model,
            'left' => $left,
            'right' => $right,
            'diffSingle' => $diffSingle,
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
     * Finds the Extension model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Extension the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $revision = null)
    {
        if (($model = Extension::find()->where(['id' => $id])->active()->one()) !== null) {

            Yii::trace(print_r($model->attributes, true));

            if ($revision === null) {
                return $model;
            }

            $revisionModel = $this->findRevision($model->id, $revision);
            Yii::trace(print_r($revisionModel->attributes, true));
            $model->loadRevision($revisionModel);
            Yii::trace(print_r($model->attributes, true));
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private $_revisions = [];
}
