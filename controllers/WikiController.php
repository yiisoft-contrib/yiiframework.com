<?php

namespace app\controllers;

use app\models\Wiki;
use app\models\WikiCategory;
use app\models\WikiRevision;
use app\models\WikiTag;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class WikiController extends Controller
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
                        'actions' => ['index', 'view', 'history', 'revision'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'list-tags', 'update'],
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
        $query = Wiki::find()->with(['creator', 'updater', 'category']);

        if ($category !== null) {
            $category = (int) $category;
            if (WikiCategory::findOne($category) === null) {
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

//        $criteria=new CDbCriteria;
//        $criteria->addCondition('t.status='.self::STATUS_PUBLISHED);
//        if(isset($filters['category']))
//            $criteria->compare('t.category_id', (int)$filters['category']);
//        if(isset($filters['tag']))
//        {
//            $criteria->addCondition("CONCAT(', ',tags,',') LIKE :tag");
//            $criteria->params[':tag']='%, '.strtr($filters['tag'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).',%';
//        }

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
            'category' => $category,
        ]);
    }

    public function actionView($id, $revision = null)
    {
        $revision = empty($revision) ? null : (int) $revision;

        $model = $this->findModel($id, $revision);

        // normalize slug URL
        $slug = Yii::$app->request->get('name');
        if ($model->slug !== $slug) {
            return $this->redirect(['wiki/view', 'id' => $model->id, 'name' => $model->slug, 'revision' => $revision], 301);
        }

        // update view count
        $model->updateCounters(['view_count' => 1]);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        // TODO
//        if(!user()->dbUser->canCreateWiki())
//            throw new CHttpException(403,'Sorry, you are too new to write a wiki article. Please try posting it in our forum first.');

        $model = new Wiki();
        $model->scenario = 'create';

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {

//            Star::model()->castStar('Wiki',$model->id,user()->id,1);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create',array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id,$revision=0)
    {
        // TODO
//        if(!user()->dbUser->canCreateWiki())
//            throw new CHttpException(403,'Sorry, you are too new to write a wiki article. Please try posting it in our forum first.');

        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {

//            if(($changes=$model->findChanges($oldAttributes))!='')
//                $model->notifyFollowers($changes);

//            Star::model()->castStar('Wiki',$model->id,user()->id,1);
            return $this->redirect(['view', 'id' => $model->id]);
        }

//        else if(($revision=(int)$revision)!==0)
//        {
//            $rev=WikiRevision::model()->findByPk(array('wiki_id'=>$model->id,'revision'=>$revision));
//            if($rev!==null)
//            {
//                $model->title=$rev->title;
//                $model->content=$rev->content;
//                $model->tags=$rev->tags;
//                $model->memo='Reverted to revision #'.$rev->revision;
//                $model->category_id=$rev->category_id;
//            }
//        }

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
                'sort' => [
                    'defaultOrder' => ['updated_at' => SORT_DESC],
                ]
            ])
        ]);
    }

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

        if ($r2 !== null) {
            $right = WikiRevision::findOne(['wiki_id' => $id, 'revision' => $r2]);
            if ($right === null) {
                throw new NotFoundHttpException('The requested revision does not exist.');
            }
        } else {
            $right = WikiRevision::findOne(['wiki_id' => $id, 'revision' => $r1 - 1]);
        }
        if ($right === null) {
            return $this->redirect(['view', 'id' => $model->id, 'name' => $model->slug]);
        }

        return $this->render('revision', [
            'model' => $model,
            'left' => $left,
            'right' => $right,
        ]);
    }


    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wiki the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $revision = null)
    {
        if (($model = Wiki::findOne($id)) !== null) {

            Yii::trace(print_r($model->attributes, true));

            if ($revision === null) {
                return $model;
            } else {
                $revisionModel = WikiRevision::findOne(['wiki_id' => $model->id, 'revision' => (int) $revision]);
                if ($revisionModel !== null) {

                    Yii::trace(print_r($revisionModel->attributes, true));
                    $model->loadRevision($revisionModel);
                    Yii::trace(print_r($model->attributes, true));
                    return $model;
                }
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
