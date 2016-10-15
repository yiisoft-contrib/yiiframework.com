<?php

namespace app\controllers;

use app\models\Wiki;
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
                        'actions' => ['index', 'view'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'list-tags'],
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
            'query' => Wiki::find()->with(['creator', 'updater']),
//            'sort'=> [
//                'attributes'=> [
//                    'create'=> [
//                        'asc'=>'t.create_time',
//                        'desc'=>'t.create_time DESC',
//                        'label'=>'Sorted by date',
//                        'default'=>'desc',
//                    ],
//                    'update'=> [
//                        'asc'=>'t.update_time',
//                        'desc'=>'t.update_time DESC',
//                        'label'=>'Sorted by date (updated)',
//                        'default'=>'desc',
//                    ],
//                    'rating'=> [
//                        'asc'=>'t.rating',
//                        'desc'=>'t.rating DESC',
//                        'label'=>'Sorted by rating',
//                        'default'=>'desc',
//                    ],
//                    'comments'=> [
//                        'asc'=>'t.comment_count',
//                        'desc'=>'t.comment_count DESC',
//                        'label'=>'Sorted by comments',
//                        'default'=>'desc',
//                    ],
//                    'views'=> [
//                        'asc'=>'t.view_count',
//                        'desc'=>'t.view_count DESC',
//                        'label'=>'Sorted by views',
//                        'default'=>'desc',
//                    ],
//                ],
//                'defaultOrder'=> ['create'=>true],
//            ],
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tag' => $tag,
            'category' => '', //isset($category) ? Lookup::item('WikiCategory',$category) : null,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        // normalize slug URL
        $slug = Yii::$app->request->get('name');
        if ($model->slug !== $slug) {
            return $this->redirect(['wiki/view', 'id' => $model->id, 'name' => $model->slug], 301);
        }

        // update view count
        $model->updateCounters(['view_count' => 1]);


        // TODO
        //$model->incrementViewCount();

//        $content=preg_replace_callback('!<h(2|3)>(.+?)</h\d>!', array($this, 'processHeadings'), $model->htmlContent);
//        if(count($this->headings)>=2 && strlen($content)>5000) // sufficiently long
//        {
//            $toc=array();
//            foreach($this->headings as $heading)
//                $toc[]="<div class=\"ref level-{$heading['level']}\">".l($heading['title'],'#'.$heading['id']).'</div>';
//            $content='<div class="toc">'.implode("\n",$toc)."</div>\n".$content;
//        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    protected $headings=array();
    protected function processHeadings($match)
    {
        $level = intval($match[1]);
        $id = 'hh'.count($this->headings);
        $title = $match[2];

        $this->headings[] = array('title' => $title, 'id' => $id, 'level'=>$level);
        $anchor = sprintf('<a class="anchor" href="#%s">¶</a>', $id);
        return sprintf('<h%d id="%s">%s %s</h%d>', $level, $id, $title, $anchor, $level);
    }

    public function actionCreate()
    {
        // TODO
//        if(!user()->dbUser->canCreateWiki())
//            throw new CHttpException(403,'Sorry, you are too new to write a wiki article. Please try posting it in our forum first.');

        $model = new Wiki();

        if ($model->load(\Yii::$app->request->post()) && $model->save()) {

//            Star::model()->castStar('Wiki',$model->id,user()->id,1);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create',array(
            'model'=>$model,
        ));
    }

    public function actionUpdate($id,$revision=0)
    {
        if(user()->isGuest)
            user()->loginRequired();

        if(!user()->dbUser->canCreateWiki())
            throw new CHttpException(403,'Sorry, you are too new to write a wiki article. Please try posting it in our forum first.');

        $model=$this->loadModel('Wiki',$id);
        if(isset($_POST['Wiki']))
        {
            $oldAttributes=$model->attributes;
            $model->attributes=$_POST['Wiki'];
            if($model->save())
            {
                if(($changes=$model->findChanges($oldAttributes))!='')
                    $model->notifyFollowers($changes);
                Star::model()->castStar('Wiki',$model->id,user()->id,1);
                $this->redirect($model->url);
            }
        }
        else if(($revision=(int)$revision)!==0)
        {
            $rev=WikiRevision::model()->findByPk(array('wiki_id'=>$model->id,'revision'=>$revision));
            if($rev!==null)
            {
                $model->title=$rev->title;
                $model->content=$rev->content;
                $model->tags=$rev->tags;
                $model->memo='Reverted to revision #'.$rev->revision;
                $model->category_id=$rev->category_id;
            }
        }

        $this->render('update',array(
            'model'=>$model,
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
        $model=$this->loadModel('Wiki',$id);
        $this->render('history',array(
            'model'=>$model,
            'revisions'=>$model->revisions,
        ));
    }

    public function actionRevision($id,$r1,$r2=0)
    {
        $model=$this->loadModel('Wiki',$id);

        $r1=(int)$r1;
        $r2=(int)$r2;
        $left=WikiRevision::model()->findByPk(array('wiki_id'=>$model->id,'revision'=>$r1));
        if($left===null)
            throw new CHttpException(404,"Unable to find the revision $r1.");
        if($r2==0) // compare with prior revision
        {
            if(($right=$left->findPriorRevision())===null)
                $right=$left->findNextRevision();
            if($right===null)
                $this->redirect($model->url);
        }
        else
        {
            $right=WikiRevision::model()->findByPk(array('wiki_id'=>$model->id,'revision'=>$r2));
            if($right===null)
                throw new CHttpException(404,"Unable to find the revision $r2.");
        }

        $this->render('revision',array(
            'model'=>$model,
            'left'=>$left,
            'right'=>$right,
        ));
    }


    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Wiki the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Wiki::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
