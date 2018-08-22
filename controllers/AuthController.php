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
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
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
                            'signup',
                        ],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
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
                            'discourse-sso'
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
        return (new AuthHandler($client))->handle();
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
        return $this->redirect(['user/profile']);
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
        return $this->redirect(['user/profile']);
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
        if (!Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('success', 'You are already logged in! In case you did not know: Your forum credentials are the same as for the website.');
            return $this->redirect(['/user/profile']);
        }

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->redirect(['/user/profile']);
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

    /**
     * This action implements the single-sign-on (SSO) meachanism for the Discourse Forum software
     *
     * @see https://meta.discourse.org/t/official-single-sign-on-for-discourse-sso/13045
     *
     * Configure SSO in Discourse to point to https://www.yiiframework.com/auth/discourse-sso
     */
    public function actionDiscourseSso($sso, $sig)
    {
        $ssoSecret = Yii::$app->params['discourse.sso_secret'];
        $discourseUrl = Yii::$app->params['discourse.sso_url'];

        // Validate the signature, ensure that HMAC-SHA256 of ssoSecret, PAYLOAD is equal to the sig
        if (hash_hmac('sha256', $sso, $ssoSecret) !== $sig) {
            throw new ForbiddenHttpException('Invalid payload signature.');
        }
        parse_str(base64_decode($sso), $inputPayload);
        if (!isset($inputPayload['nonce'])) {
            throw new ForbiddenHttpException('Invalid payload.');
        }

        // if email is not validated, redirect to profile
        /** @var $user User */
        $user = Yii::$app->user->identity;
        if (!$user->email_verified) {
            Yii::$app->session->setFlash('error', 'You need to validate your email address before being able to log in to the forum.');
            return $this->redirect(['/user/profile']);
        }

        // Create a new payload with nonce, email, external_id and optionally (username, name)
        $userPayload = [
            // nonce should be copied from the input payload
            'nonce' => $inputPayload['nonce'],
            // email must be a verified email address. If the email address has not been verified, set require_activation to “true”.
            'email' => $user->email,
            // external_id is any string unique to the user that will never change, even if their email, name, etc change. The suggested value is your database’s ‘id’ row number.
            'external_id' => $user->id,
            // username will become the username on Discourse if the user is new or SiteSetting.sso_overrides_username is set.
            'username' => $user->username,
            // name will become the full name on Discourse if the user is new or SiteSetting.sso_overrides_name is set.
            'name' => $user->display_name,
            // avatar_url will be downloaded and set as the user’s avatar if the user is new or SiteSetting.sso_overrides_avatar is set.
            'avatar_url' => Url::to(['user/avatar', 'id' => $user->id]),
            // avatar_force_update is a boolean field. If set to true, it will force Discourse to update the user’s avatar, whether avatar_url has changed or not.
            'avatar_force_update' => true,
            // bio will become the contents of the user’s bio if the user is new, their bio is empty or SiteSetting.sso_overrides_bio is set.
            'bio' => '',
            // Additional boolean (“true” or “false”) fields are: admin, moderator, suppress_welcome_message
        ];
        // Base64 encode the payload
        $userPayloadEncoded = base64_encode(http_build_query($userPayload));
        // Calculate a HMAC-SHA256 hash of the payload using ssoSecret as the key and Base64 encoded payload as text
        $userSig = hash_hmac('sha256', $userPayloadEncoded, $ssoSecret);
        // Redirect back to http://discourse_site/session/sso_login?sso=payload&sig=sig
        return $this->redirect(rtrim($discourseUrl, '/') . '/session/sso_login?' . http_build_query(['sso' => $userPayloadEncoded, 'sig' => $userSig]));
    }
}
