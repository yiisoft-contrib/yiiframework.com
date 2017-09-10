<?php
namespace app\models;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            User::usernameRules(),
            User::emailRules(), [

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ]);
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
            if (!$user->save(false)) {
                return null;
            }

            $this->sendEmailVerificationEmail($user);

            return $user;
        }

        return null;
    }

    private function sendEmailVerificationEmail(User $user)
    {
        if ($user) {
            if (!User::isEmailVerificationTokenValid($user->email_verification_token)) {
                $user->generateEmailVerificationToken();
            }

            if ($user->save(false)) {
                return \Yii::$app->mailer->compose([
                    'html' => 'signupVerifyEmail-html',
                    'text' => 'signupVerifyEmail-text',
                ], ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($user->email)
                    ->setSubject('Welcome to Yii community!')
                    ->send();
            }
        }

        return false;
    }
}
