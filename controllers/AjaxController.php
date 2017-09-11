<?php

namespace app\controllers;

use app\models\ActiveRecord;
use app\models\Rating;
use app\models\Star;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * AjaxController handles several ajax actions in the background
 */
class AjaxController extends BaseController
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
                'denyCallback' => function() {
                    if (Yii::$app->user->getIsGuest()) {
                        // redirect the site to login when someone clicks on a button that needs login
                        Yii::$app->user->loginRequired(true, false);
                    } else {
                        throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                    }
                }
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'vote' => ['post'],
                    'star' => ['post']
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

    /**
     * Casts a star to the specified content object.
     *
     * @param string $type the model type to add the star.
     * @param integer $id the ID of the model.
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionStar($type, $id)
    {
        $model = null;
        if (in_array($type, Star::$modelClasses, true)) {
            /** @var $modelClass ActiveRecord */
            $modelClass = "app\\models\\$type";
            $model = $modelClass::findOne($id);
        }

        if ($model === null) {
            throw new NotFoundHttpException();
        }

        $star = Star::castStar($model, Yii::$app->user->id);
        $starCount = Star::getStarCount($model);

        return [
            'star' => $star,
            'starCount' => $starCount
        ];
    }
}
