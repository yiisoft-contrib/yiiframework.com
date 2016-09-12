<?php

namespace app\controllers;

use yii\web\Controller;

class WikiController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }
}
