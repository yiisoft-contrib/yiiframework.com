<?php

namespace app\models;

use app\components\forum\ForumAdapterInterface;
use Yii;
use yii\base\Model;

/**
 * @property User $user
 */
class ChangeEmailForm extends Model
{
    /**
     * @var string
     */
    public $currentPassword;
    /**
     * @var string
     */
    public $email;

    /**
     * @var User
     */
    private $_user;

    public function __construct(User $user, array $config = [])
    {
        $this->_user = $user;

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['currentPassword', 'email'], 'required'],
            ['currentPassword', 'validateCurrentPassword'],
            ['email', 'email'],
        ];
    }

    public function validateCurrentPassword()
    {
        if (!$this->user->validatePassword($this->currentPassword)) {
            $this->addError('currentPassword', 'The current password is incorrect.');
        }
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'currentPassword' => 'Current password',
            'email' => 'New email',
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->email = $this->email;
            $this->user->email_verified = false;
            $this->user->generateAuthKey();
            $this->user->removePasswordResetToken();
            return $this->user->save(false);
        }

        return false;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->_user;
    }

}
