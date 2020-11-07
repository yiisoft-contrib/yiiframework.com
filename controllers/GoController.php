<?php


namespace app\controllers;


use yii\web\Controller;

class GoController extends Controller
{
    public function actionSlack()
    {
        return $this->redirect(\Yii::$app->params['slack.invite.link']);
    }
}
