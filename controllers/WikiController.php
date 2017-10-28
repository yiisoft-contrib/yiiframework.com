<?php

namespace app\controllers;

use app\components\UserPermissions;
use app\models\Star;
use app\models\Wiki;
use app\models\WikiCategory;
use app\models\WikiRevision;
use app\models\WikiTag;
use app\notifications\WikiUpdateNotification;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class WikiController extends BaseController
{
    public $sectionTitle = 'Yii Framework Wiki';
    public $headTitle = 'Wiki';

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
                        'actions' => ['index', 'view', 'history', 'revision'],
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

    public function actionIndex($category = null, $tag = null, $version = '2.0')
    {
        if (!in_array($version, [Wiki::YII_VERSION_10, Wiki::YII_VERSION_11, Wiki::YII_VERSION_20, Wiki::YII_VERSION_ALL], true)) {
            throw new NotFoundHttpException();
        }

        $query = Wiki::find()->active()->with(['creator', 'updater', 'category']);

        $categoryModel = null;
        if ($category !== null) {
            $category = (int) $category;
            if (($categoryModel = WikiCategory::findOne($category)) === null) {
                throw new NotFoundHttpException('The requested category does not exist.');
            }
            $query->andWhere(['category_id' => $category]);
        }

        $tagModel = null;
        if ($tag !== null) {
            $tagModel = WikiTag::findOne(['slug' => $tag]);
            if ($tagModel === null) {
                throw new NotFoundHttpException('The requested tag does not exist.');
            }
            $query->joinWith('tags', false);
            $query->andWhere(['wiki_tag_id' => $tagModel->id]);
        }

        if ($version && $version !== 'all') {
            $query->andWhere(['or', ['yii_version' => $version], ['yii_version' => 'all']]);
        } else {
            $version = Wiki::YII_VERSION_ALL;
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
                    'views'=> [
                        'asc'=>['view_count' => SORT_ASC],
                        'desc'=>['view_count' => SORT_DESC],
                        'label'=>'Sorted by views',
                        'default'=>'desc',
                    ],
                ],
                'defaultOrder'=> ['create'=>SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tag' => $tagModel,
            'category' => $categoryModel,
            'version' => $version,
        ]);
    }

    public function actionView($id, $revision = null)
    {
        $revision = empty($revision) ? null : (int) $revision;

        $model = $this->findModel($id, $revision);

        // normalize slug URL
        $slug = Yii::$app->request->get('name');
        if ($model->slug !== (string) $slug) {
            return $this->redirect(['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $revision], 301);
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
        if (!UserPermissions::canCreateWikiPage()) {
            throw new ForbiddenHttpException('Please go to your profile and validate email before creating a wiki article.');
        }

        $model = new Wiki();
        $model->loadDefaultValues();
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
        $model = $this->findModel($id, $revision);

        if (!UserPermissions::canUpdateWikiPage($model)) {
            throw new ForbiddenHttpException('Sorry, you are too new to write a wiki article. Please try posting it in our forum first.');
        }

        $model->scenario = 'update';

        if ($revision !== null) {
            $rev = $this->findRevision($id, $revision);
            $model->memo='Reverted to revision #' . $rev->revision;
        }

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {

            if ($model->savedRevision !== null) {
                // update timestamps from DB
                $model->refresh();
                $model->savedRevision->refresh();
                // notify followers
                WikiUpdateNotification::create([
                    'wiki' => $model,
                    'updater' => Yii::$app->user->identity,
                    'changes' => $model->savedRevision,
                ]);
            }

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
        $models = WikiTag::find()->where(['like', 'name', $query])->all();
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

        $left = WikiRevision::findOne(['wiki_id' => $id, 'revision' => $r1]);
        if ($left === null) {
            throw new NotFoundHttpException('The requested revision does not exist.');
        }
        $model = $left->wiki;
        if ($model->status !== Wiki::STATUS_PUBLISHED) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($r2 !== null) {
            if ($r2 === 'latest') {
                $right = WikiRevision::findLatest($id);
            } else {
                $right = WikiRevision::findOne(['wiki_id' => $id, 'revision' => $r2]);
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
     * Finds the Wiki model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wiki the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $revision = null)
    {
        if (($model = Wiki::find()->where(['id' => $id])->active()->one()) !== null) {

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

    /**
     * @param $id
     * @param $revision
     * @return WikiRevision
     * @throws NotFoundHttpException
     */
    protected function findRevision($id, $revision)
    {
        if (isset($this->_revisions[$id][$revision])) {
            return $this->_revisions[$id][$revision];
        }
        $model = WikiRevision::findOne(['wiki_id' => $id, 'revision' => (int) $revision]);

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $this->_revisions[$id][$revision] = $model;
        return $model;
    }
}
