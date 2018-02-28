<?php


namespace app\components\mailers;

use app\models\User;

class EmailVerificationMailer implements MailerInterface
{
    private $_user;
    private $_type;

    const TYPE_PROFILE = 'profile';
    const TYPE_SIGNUP = 'signup';

    /**
     * EmailVerificationMailer constructor.
     * @param User $user
     */
    public function __construct(User $user, $type)
    {
        $this->_user = $user;
        $this->_type = $type;
    }


    public function send()
    {
        if (!User::isEmailVerificationTokenValid($this->_user->email_verification_token)) {
            $this->_user->generateEmailVerificationToken();
        }

        if ($this->_user->save(false)) {
            $this->sendEmail();
        }

        return false;
    }

    private function sendEmail()
    {
        if ($this->_type === self::TYPE_PROFILE) {
            return \Yii::$app->mailer->compose([
                'html' => 'profileVerifyEmail-html',
                'text' => 'profileVerifyEmail-text',
            ], ['user' => $this->_user])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                ->setTo($this->_user->email)
                ->setSubject('Please confirm your email at Yii community')
                ->send();
        }

        if ($this->_type === self::TYPE_SIGNUP) {
            return \Yii::$app->mailer->compose([
                'html' => 'signupVerifyEmail-html',
                'text' => 'signupVerifyEmail-text',
            ], ['user' => $this->_user])
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                ->setTo($this->_user->email)
                ->setSubject('Welcome to Yii community!')
                ->send();

        }
        return false;
    }
}
