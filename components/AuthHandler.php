<?php
namespace app\components;

use app\components\forum\ForumAdapterInterface;
use Helge\SpamProtection\SpamProtection;
use Yii;
use app\models\Auth;
use app\models\User;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\web\Response;

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
     * @return Response|null
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

        // use login for displayname if it is not filled in github account
        if (empty($fullname)) {
            $fullname = $login;
        }

        /** @var Auth $auth */
        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) { // login
                $this->login($auth);
            } else { // signup
                return $this->signup($email, $login, $fullname, $id);
            }
        } else { // user already logged in
            if (!$auth) { // add auth provider
                $this->addAuthProvider($id, $login);
            } else { // there's existing auth
                if ($auth->user_id == Yii::$app->user->id) {
                    // do nothing, login is already active
                } else {
                    $this->clientAlreadyInUse();
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

    /**
     * @param $auth
     */
    private function login($auth)
    {
        /** @var User $user */
        $user = $auth->user;
        $this->updateUserInfo($user);
        Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
    }

    /**
     * @param $email
     * @param $login
     * @param $fullname
     * @param $id
     * @return \yii\console\Response|Response
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    private function signup($email, $login, $fullname, $id)
    {
        $spamProtection = new SpamProtection();

        if ($spamProtection->checkEmail($email)) {
            Yii::info('Spam protection: GitHub auth flagged with email ' . $email);
            Yii::$app->getSession()->setFlash('error', [
                'Sorry, we cannot let you in. If you are sure you need to enter, please contact Yii team.',
            ]);
            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }

        if ($spamProtection->checkUsername($login)) {
            Yii::info('Spam protection: GitHub auth flagged with username ' . $login);
            Yii::$app->getSession()->setFlash('error', [
                'Sorry, we cannot let you in. If you are sure you need to enter, please contact Yii team.',
            ]);
            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }


        if ($email !== null && User::find()->where(['email' => $email])->exists()) {
            Yii::$app->getSession()->setFlash('error', [
                Yii::t('app',
                    "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.",
                    ['client' => $this->client->getTitle()]),
            ]);
            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }
        if ($login !== null && User::find()->where(['username' => $login])->exists()) {
            Yii::$app->getSession()->setFlash('error', [
                Yii::t('app',
                    "User with the same name as in {client} account already exists but isn't linked to it. Login in to link your existing account to {client}, or sign up manually if you do not have an account.",
                    ['client' => $this->client->getTitle()]),
            ]);
            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }

        $user = new User();

        $user->username = $login;
        $user->display_name = $fullname;
        $password = \Yii::$app->security->generateRandomString(6);
        $user->password = $password;
        $user->email = $email;
        $this->updateUserInfo($user);

        if (!empty($email)) {
            $user->email_verified = true;
        }

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

                /** @var ForumAdapterInterface $forumAdapter */
                $forumAdapter = Yii::$app->forumAdapter;
                $forumID = $forumAdapter->ensureForumUser($user, $password);
                $user->forum_id = $forumID;
                $user->save(false);

                $transaction->commit();

                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', [
                    Yii::t('app', 'Unable to save {client} account: {errors}', [
                        'client' => $this->client->getTitle(),
                        'errors' => json_encode($auth->getErrors(), JSON_UNESCAPED_UNICODE),
                    ]),
                ]);
                return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
            }
        } else {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', [
                Yii::t('app', 'Unable to save user: {errors}', [
                    'client' => $this->client->getTitle(),
                    'errors' => json_encode($user->getErrors(), JSON_UNESCAPED_UNICODE),
                ]),
            ]);
            return Yii::$app->response->redirect(Yii::$app->user->loginUrl);
        }
    }

    /**
     * @param $id
     * @param $login
     */
    private function addAuthProvider($id, $login)
    {
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
    }

    private function clientAlreadyInUse()
    {
        Yii::$app->getSession()->setFlash('error', [
            Yii::t('app',
                'Unable to link {client} account. There is another user using it.',
                ['client' => $this->client->getTitle()]),
        ]);
    }
}
