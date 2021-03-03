<?php

namespace app\models;

use app\components\forum\ForumAdapterInterface;
use Yii;
use yii\base\Model;

/**
 * @property User $user
 */
class ChangePasswordForm extends Model
{
    /**
     * @var string
     */
    public $currentPassword;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $passwordRepeat;

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
            [['currentPassword', 'password', 'passwordRepeat'], 'required'],
            ['currentPassword', 'validateCurrentPassword'],
            ['password', 'string', 'min' => 6],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
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
            'password' => 'New password',
            'passwordRepeat' => 'Repeat password',
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->setPassword($this->password);
            $this->user->save(false);
            $this->user->generateAuthKey();

            /** @var ForumAdapterInterface $forumAdapter */
            $forumAdapter = Yii::$app->forumAdapter;
            $forumAdapter->changeUserPassword($this->user, $this->password);

            return true;
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
