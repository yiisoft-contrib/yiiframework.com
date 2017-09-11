<?php

namespace app\controllers;

use app\components\AuthHandler;
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
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * AuthController handles user authentification, i.e. Login, Signup and OAuth login.
 */
class AuthController extends BaseController
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
                        'actions' => [
                            'request-password-reset',
                            'reset-password',
                            'auth',
                            'verify-email',
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'signup',
                            'login',
                        ],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [
                            'logout',
                            'remove-auth',
                            'connect-auth',
                            'disable-password',
                        ],
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
        (new AuthHandler($client))->handle();
    }

    /**
     * Connect a logged in account via OAuth and redirects back to user profile.
     */
    public function actionConnectAuth($source)
    {
        if ($source !== 'github') {
            // more auth sources may be added later
            throw new NotFoundHttpException();
        }
        // ensure redirection to profile after OAuth workflow
        Yii::$app->user->setReturnUrl(['/user/profile']);
        return $this->redirect(['auth', 'authclient' => $source]);
    }

    /**
     * Remove specified auth source from an account.
     */
    public function actionRemoveAuth($source)
    {
        if ($source !== 'github') {
            // more auth sources may be added later
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
        }

        return $this->render('login', [
            'model' => $model,
        ]);
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
                if (Yii::$app->getUser()->login($user)) {
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
            /** @var User $user */
            $user = Yii::$app->user->identity;
            $model->email = $user->email;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return Yii::$app->user->isGuest ? $this->goHome() : $this->redirect(['user/profile']);
            }
            Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
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

            return Yii::$app->user->isGuest ? $this->goHome() : $this->redirect(['user/profile']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail($token)
    {
        if (User::validateEmailVerificationToken($token)) {
            Yii::$app->getSession()->setFlash('success', 'Email was verified successfully.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Unable to verifiy email.');
        }

        return Yii::$app->user->isGuest ? $this->goHome() : $this->redirect(['user/profile']);
    }
}
