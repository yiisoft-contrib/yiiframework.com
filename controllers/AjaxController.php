<?php

namespace app\controllers;

use app\models\Rating;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * AjaxController handles several ajax actions in the background
 */
class AjaxController extends Controller
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'vote' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * Casts a user vote up/down on an item, e.g. a comment
     *
     * @param string $type the model type to add the vote for e.g. "Comment"
     * @param integer $id the ID of the model.
     * @param integer $vote 1 for upvote, 0 for downvote
     * @return array updated vote count for that model
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionVote($type, $id, $vote)
    {
        if (in_array($type, Rating::$modelClasses, true)) {
            /** @var $modelClass ActiveRecord */
            $modelClass = "app\\models\\$type";
            $model = $modelClass::findOne((int) $id);
        }
        if (!isset($model)) {
            throw new NotFoundHttpException();
        }

        list($total, $up) = Rating::castVote($model, Yii::$app->user->id, $vote);
        return [
            'up' => $up,
            'down' => $total - $up,
            'total' => $total,
        ];
    }
}
