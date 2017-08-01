<?php
namespace app\components;

use Yii;
use app\models\Auth;
use app\models\User;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 *  AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * AuthHandler constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Handles by the $client
     */
    public function handle()
    {
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');
        $login = ArrayHelper::getValue($attributes, 'login');
        $fullname = ArrayHelper::getValue($attributes, 'name');

        if ($this->client->getName() === 'twitter') {
            $login = ArrayHelper::getValue($attributes, 'screen_name');
        }

        if ($this->client->getName() === 'facebook') {
            $login = ArrayHelper::getValue($attributes, 'id');
        }

        /** @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                /** @var User $user */
                $user = $auth->user;
                $this->updateUserInfo($user);
                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
            } else { // signup
                if ($email !== null && User::find()->where(['email' => $email])->exists()) {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app',
                            "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.",
                            ['client' => $this->client->getTitle()]),
                    ]);
                } else {
                    $user = new User();

                    $user->username = $login;
                    $user->display_name = $fullname;
                    $user->password = \Yii::$app->security->generateRandomString(6);
                    $user->email = $email;

//                    if ($this->client->getName() === 'twitter') {
//                        $user->twitter = $login;
//                    }
//
//                    if ($this->client->getName() === 'facebook') {
//                        $user->facebook = $login;
//                    }
//
//                    if ($this->client->getName() === 'github') {
//                        $user->github = $login;
//                    }

                    $user->generateAuthKey();
                    $user->generatePasswordResetToken();

                    $transaction = User::getDb()->beginTransaction();

                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $this->client->getId(),
                            'source_id' => (string)$id,
                            'source_login' => (string)$login,
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                        } else {
                            Yii::$app->getSession()->setFlash('error', [
                                Yii::t('app', 'Unable to save {client} account: {errors}', [
                                    'client' => $this->client->getTitle(),
                                    'errors' => json_encode($auth->getErrors()),
                                ]),
                            ]);
                        }
                    } else {
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('app', 'Unable to save user: {errors}', [
                                'client' => $this->client->getTitle(),
                                'errors' => json_encode($user->getErrors(), JSON_UNESCAPED_UNICODE),
                            ]),
                        ]);
                    }
                }
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$id,
                    'source_login' => $login,
                ]);
                if ($auth->save()) {
                    /** @var User $user */
                    $user = $auth->user;
                    $this->updateUserInfo($user);
                    Yii::$app->getSession()->setFlash('success', [
                        Yii::t('app', 'Linked {client} account.', [
                            'client' => $this->client->getTitle()
                        ]),
                    ]);
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app', 'Unable to link {client} account: {errors}', [
                            'client' => $this->client->getTitle(),
                            'errors' => json_encode($auth->getErrors()),
                        ]),
                    ]);
                }
            } else { // there's existing auth
                if ($auth->user_id == Yii::$app->user->id) {
                    $transaction = Auth::getDb()->beginTransaction();
                    if ($auth->delete()) {
                        $user = $auth->user;
                        $user->{$this->client->getId()} = null;
                        if ($user->save()) {
                            $transaction->commit();
                            Yii::$app->getSession()->setFlash('success', [
                                Yii::t('app',
                                    'Social network {client} has been successfully disabled.',
                                    ['client' => $this->client->getTitle()]),
                            ]);
                        }
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('app',
                            'Unable to link {client} account. There is another user using it.',
                            ['client' => $this->client->getTitle()]),
                    ]);
                }
            }
        }
    }

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $attributes = $this->client->getUserAttributes();

//        if ($this->client->getName() === 'github') {
//            $user->github = ArrayHelper::getValue($attributes, 'login');
//        }
//
//        if ($this->client->getName() === 'twitter') {
//            $user->twitter = ArrayHelper::getValue($attributes, 'screen_name');
//        }
//
//        if ($this->client->getName() === 'facebook') {
//            $user->facebook = ArrayHelper::getValue($attributes, 'id');
//        }

        if (!$user->display_name) {
            $user->display_name = ArrayHelper::getValue($attributes, 'name');
        }

        $user->save();
    }
}
