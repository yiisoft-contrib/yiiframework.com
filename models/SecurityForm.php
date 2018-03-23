<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SecurityForm is the model behind the contact form.
 */
class SecurityForm extends Model
{
    public $name;
    public $email;
    public $body;
    public $verifyCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email', 'body'], 'required'],
            ['name', 'email'],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
            'name' => 'Your Email',
            'email' => 'Your Name',
            'body' => 'Message',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @return boolean whether the model passes validation
     */
    public function send()
    {
        if ($this->validate()) {
            $fromEmail = $this->name;
            $name = $this->email;

            Yii::$app->mailer->compose()
                ->setCc(Yii::$app->params['securityEmails'])
                ->setFrom([$fromEmail => $name])
                ->setSubject('[Security] Report by ' . $name)
                ->setTextBody($this->body)
                ->send();

            return true;
        }
        return false;
    }
}
