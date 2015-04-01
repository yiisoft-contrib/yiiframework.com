<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionBooks()
    {
        return $this->render('books');
    }

    public function actionContribute()
    {
        return $this->render('contribute');
    }

    public function actionChat()
    {
        return $this->render('chat');
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionNews()
    {
        return $this->render('news');
    }

    public function actionLicense()
    {
        return $this->render('license');
    }

    public function actionTeam()
    {
        $members = Yii::$app->params['members'];

        $contributorLimit = 1000;

        // getting contributors from github
        $cacheKey = __CLASS__ . ":team:contributors:$contributorLimit";
        if (($contributors = Yii::$app->cache->get($cacheKey)) === false) {

            $client = new \Github\Client();
            $api = $client->api('repo');
            $paginator  = new \Github\ResultPager($client);
            $parameters = ['yiisoft', 'yii2'];
            $contributors = $paginator->fetch($api, 'contributors', $parameters);
            while($paginator->hasNext() && count($contributors) < $contributorLimit) {
                $contributors = array_merge($contributors, $paginator->fetchNext());
            }

            // remove team members
            $teamGithubs = array_filter(array_map(function($member) { return isset($member['github']) ? $member['github'] : false; }, $members));
            foreach($contributors as $key => $contributor) {
                if (in_array($contributor['login'], $teamGithubs)) {
                    unset($contributors[$key]);
                }
            }

            $contributors = array_slice($contributors, 0, $contributorLimit);
            Yii::$app->cache->set($cacheKey, $contributors, 3600 * 12); // cache for 12hours
        }

        return $this->render('team', [
            'members' => $members,
            'contributors' => $contributors,
        ]);
    }

    public function actionReportIssue()
    {
        return $this->render('report-issue');
    }

    public function actionSecurity()
    {
        return $this->render('security');
    }

}
