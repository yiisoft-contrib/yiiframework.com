<?php

namespace app\controllers;

use app\components\RowHelper;
use app\models\Auth;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
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

    /**
     * @inheritdoc
     */
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
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * @param ClientInterface $client
     */
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');

        /** @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                $user = $auth->user;
                Yii::$app->user->login($user, 3600 * 24 * 30);
            } else { // signup
                if (User::find()->where(['email' => $email])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $client->getTitle()]),
                    ]);
                } else {
                    $password = Yii::$app->security->generateRandomString(6);
                    $user = new User([
                        'username' => $attributes['login'],
                        'email' => $email,
                        'password' => $password,
                    ]);
                    $user->generateAuthKey();
                    $user->generatePasswordResetToken();

                    $transaction = $user->getDb()->beginTransaction();

                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $client->getId(),
                            'source_id' => (string)$attributes['id'],
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user, 3600 * 24 * 30);
                        } else {
                            print_r($auth->getErrors());
                            die();
                        }
                    } else {
                        print_r($user->getErrors());
                        die();
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $client->getId(),
                    'source_id' => $attributes['id'],
                ]);
                $auth->save();
            }
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * This action redirects old urls to the new location.
     */
    public function actionRedirect($url)
    {
        $urlMap = [
            'doc/terms' => ['site/license', '#' => 'docs'],
        ];
        if (isset($urlMap[$url])) {
            return $this->redirect($urlMap[$url], 301); // Moved Permanently
        } else {
            throw new NotFoundHttpException('The requested page was not found.');
        }
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

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user, 3600 * 24 * 30)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
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

        $activeMembers = [];
        $pastMembers = [];

        foreach ($members as $member) {
            if ($member['active']) {
                $activeMembers[] = $member;
            } else {
                $pastMembers[] = $member;
            }
        }

        $activeMembers = RowHelper::split($activeMembers, 3);
        $pastMembers = RowHelper::split($pastMembers, 3);

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
            'activeMembers' => $activeMembers,
            'pastMembers' => $pastMembers,
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

    public function actionDownload()
    {
        return $this->render('download');
    }

    public function actionTos()
    {
        return $this->render('tos');
    }

    public function actionPerformance()
    {
        $this->redirect(['index']);
    }

    public function actionDemos()
    {
        $this->redirect(['index']);
    }

    public function actionLogo()
    {
        return $this->render('logo');
    }

    /**
     * used to download specific files
     */
    public function actionFile($category, $file)
    {
        if (!preg_match('~^[\w\d-.]+$~', $file)) {
            throw new NotFoundHttpException('The requested page was not found.');
        }

        switch($category)
        {
            case 'docs-offline':
                $filePath = Yii::getAlias("@app/data/docs-offline/$file");
                if (file_exists($filePath)) {
                    return Yii::$app->response->sendFile($filePath, $file);
                }
                break;
        }
        throw new NotFoundHttpException('The requested page was not found.');
    }
}
