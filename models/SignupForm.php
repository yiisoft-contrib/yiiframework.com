<?php

namespace app\models;

use app\components\forum\ForumAdapterInterface;
use app\components\mailers\EmailVerificationMailer;
use Helge\SpamProtection\SpamProtection;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $recaptcha = [];
        if (Yii::$app->params['recaptcha.enabled']) {
            $recaptcha = [
                ['reCaptcha', \himiklab\yii2\recaptcha\ReCaptchaValidator::class],
            ];
        }
        return array_merge(
            $recaptcha,
            User::usernameRules(),
            User::emailRules(), [
            [['username', 'email'], 'checkSpam'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ]);
    }

    public function checkSpam($attribute, $params, $validator)
    {
        $spamProtection = new SpamProtection();

        $message = 'Sorry, we can not register you. If you think it is a mistake, contact Yii team.';

        $value = $this->$attribute;
        if ($attribute === 'username' && $spamProtection->checkUsername($value)) {
            $this->addError('username', $message);
        }

        if ($attribute === 'email' && $spamProtection->checkEmail($value)) {
            // using username here on purpose
            $this->addError('username', $message);
        }
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->display_name = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);

            /** @var ForumAdapterInterface $forumAdapter */
            $forumAdapter = Yii::$app->forumAdapter;
            $forumID = $forumAdapter->ensureForumUser($user, $this->password);
            $user->forum_id = $forumID;

            if (!$user->save(false)) {
                return null;
            }

            (new EmailVerificationMailer($user, EmailVerificationMailer::TYPE_SIGNUP))->send();

            return $user;
        }

        return null;
    }
}
