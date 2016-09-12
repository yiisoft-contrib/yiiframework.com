<?php

namespace app\controllers;

use app\models\Auth;

use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AuthController handles user authentification, i.e. Login, Signup and OAuth login.
 */
class AuthController extends Controller
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
                        'actions' => ['signup', 'login', 'request-password-reset', 'reset-password', 'auth'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'auth', 'request-password-reset'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['remove-auth', 'connect-auth', 'disable-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'remove-auth' => ['post'],
                    'connect-auth' => ['post'],
                    'disable-password' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
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
                            'source_login' => (string)$attributes['login'],
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
                    'source_id' => (string)$attributes['id'],
                    'source_login' => (string)$attributes['login'],
                ]);
                $auth->save(false);
            }
        }
    }

    /**
     * Connect a logged in account via OAuth and redirects back to user profile.
     */
    public function actionConnectAuth($source)
    {
        if ($source !== 'github') {
            throw new NotFoundHttpException();
        }
        Yii::$app->user->setReturnUrl(['/user/profile']);
        return $this->redirect(['auth', 'authclient' => $source]);
    }

    /**
     * Remove specified auth source from an account.
     */
    public function actionRemoveAuth($source)
    {
        if ($source !== 'github') {
            throw new NotFoundHttpException();
        }

        /** @var $user User */
        $user = Yii::$app->user->identity;
        $auth = $user->getAuthClients()->where(['source' => $source])->one();
        if ($auth !== null) {
            $auth->delete();
            Yii::$app->session->setFlash('success', 'You successfully removed ' . ucfirst($source) . ' login from your account.');
        } else {
            Yii::$app->session->setFlash('error', 'Your account has no ' . ucfirst($source) . ' login.');
        }
        $this->redirect(['user/profile']);
    }


    /**
     * Disable password login for a user.
     */
    public function actionDisablePassword()
    {
        /** @var $user User */
        $user = Yii::$app->user->identity;
        $user->disablePassword();
        Yii::$app->session->setFlash('success', 'You successfully disabled password login for your account.');
        $this->redirect(['user/profile']);
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

        // prefill the form if it is requested by a logged in user
        if (!Yii::$app->user->isGuest) {
            $model->email = Yii::$app->user->identity->email;
        }

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
}
