<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Partners form is the model behind the /partners/ page.
 *
 * Name and email are swapped on purpose.
 */
class PartnersForm extends Model
{
    public $name;
    public $email;
    public $company;
    public $body;
    public $budget;
    public $when;
    public $verifyCode;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'email', 'company', 'body', 'budget', 'when'], 'required'],
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
            'body' => 'Project details',
            'budget' => 'Budget',
            'when' => 'When do you want to start?',
            'company' => 'Company',
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

            $body = <<<HTML
                <ul>
                    <li><strong>Budget:</strong> {$this->budget}</li>
                    <li><strong>Start time:</strong> {$this->when}</li>
                </ul>
                {$this->body}
            HTML;
            Yii::$app->mailer->compose()
                ->setCc(Yii::$app->params['partnerEmails'])
                ->setFrom('partner@yiiframework.com')
                ->setReplyTo([$fromEmail => $name])
                ->setSubject('[Partner] By ' . $name . ' from ' . $this->company)
                ->setHtmlBody($body)
                ->send();

            return true;
        }
        return false;
    }
}
