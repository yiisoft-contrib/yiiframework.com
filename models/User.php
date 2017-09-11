<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property int $rating
 * @property int $rank
 * @property int $extension_count
 * @property int $wiki_count
 * @property int $comment_count
 * @property int $post_count
 * @property string $display_name
 * @property string $login_time
 * @property int $login_attempts
 * @property string $login_ip
 * @property string $email_verification_token
 * @property boolean $email_verified
 *
 * Relations:
 *
 * @property Auth[] $authClients
 * @property Wiki[] $wikis
 * @property Extension[] $extensions
 * @property Badge[] $badges
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            static::usernameRules(),
            static::emailRules(), [

            ['display_name', 'filter', 'filter' => 'trim'],
            ['display_name', 'required'],
            ['display_name', 'string', 'min' => 2, 'max' => 64],

            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ]);
    }

    public static function usernameRules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 64],
        ];
    }

    public static function emailRules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'filter', 'filter' => function($value) { return mb_strtolower($value, Yii::$app->charset); }],
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'There is already an account with this email address.'],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        // generate auth_key on creation
        if ($insert && $this->auth_key === null) {
            $this->generateAuthKey();
        }

        if ($this->isAttributeChanged('email')) {
            $this->email_verified = false;
            $this->generateEmailVerificationToken();
        }

        return true;
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return Yii::createObject(UserQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $user = static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);

        if (!$user) {
            return null;
        }

        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return $user;
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (strpos($this->password_hash, '$') === 0) {
            // up to date password hash
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        }

        // handle legacy password hashes
        if (preg_match('~^LEGACYMD5:([a-f0-9]{32}):(.+)$~', $this->password_hash, $matches)) {
            $valid = $this->validateLegacyPasswordMD5($password, $matches[1], $matches[2]);
        } elseif (preg_match('~^LEGACYSHA:([a-f0-9]+)$~', $this->password_hash, $matches)) {
            $valid = $this->validateLegacyPasswordSHA($password, $matches[1]);
        } else {
            return false;
        }

        // update password hash when password is valid
        if ($valid) {
            $this->setPassword($password);
            $this->updateAttributes(['password_hash']);
        }
        return $valid;
    }

    public function getPasswordType()
    {
        if (strpos($this->password_hash, '$') === 0) {
            return 'NEW';
        }
        if (strpos($this->password_hash, 'LEGACYMD5:') === 0) {
            return 'LEGACYMD5';
        }
        if (strpos($this->password_hash, 'LEGACYSHA:') === 0) {
            return 'LEGACYSHA';
        }
        return 'NONE';
    }

    /**
     * Validate IPB Password usingan old SHA1 method
     */
    private function validateLegacyPasswordSHA($password, $hash)
    {
        $password=$this->parseLegacyPasswordValue($password);
        return (sha1(strtolower($this->username) . $password) === $hash);
    }

    /**
     * Validate IPB Password using an old MD5 method
     */
    private function validateLegacyPasswordMD5($password, $hash, $salt)
    {
        $password=$this->parseLegacyPasswordValue($password);
        return (md5(md5($salt).md5($password)) === $hash);
    }

    /*
     * Parse value (used in IPB to clean _GET _POST values)
     * NOTE: function taken from <IPB 3.1.2 source>/admin/sources/base/core.php line 4703
     */
    private function parseLegacyPasswordValue($val)
    {
        $val = str_replace("&", "&amp;", $val);
        $val = str_replace("<!--", "&#60;&#33;--", $val);
        $val = str_replace("-->", "--&#62;", $val);
        $val = str_ireplace("<script", "&#60;script", $val);
        $val = str_replace(">", "&gt;", $val);
        $val = str_replace("<", "&lt;", $val);
        $val = str_replace('"', "&quot;", $val);
        $val = str_replace("\n", "<br />", $val);
        $val = str_replace("$", "&#036;", $val);
        $val = str_replace("!", "&#33;", $val);
        $val = str_replace("'", "&#39;", $val);
        $val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val);
        $val = preg_replace("/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val);
        return $val;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Disables password login by setting password_hash to empty.
     */
    public function disablePassword()
    {
        $this->updateAttributes(['password_hash' => '']);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'E-mail',
            'status' => 'Status',
            'created_at' => 'Member Since',
            'post_count' => 'Forum Posts',
            'extension_count' => 'Extensions',
            'wiki_count' => 'Wiki Articles',
            'comment_count' => 'Comments',

        ];
    }

    public function getStatusLabel()
    {
        $statuses = static::getStatuses();
        return ArrayHelper::getValue($statuses, $this->status);
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_DELETED => 'Delete',
            self::STATUS_ACTIVE  => 'Active',
        ];
    }

    /**
     * @return WikiQuery
     */
    public function getWikis()
    {
        return $this->hasMany(Wiki::class, ['creator_id' => 'id']);
    }

    /**
     * @return ExtensionQuery
     */
    public function getExtensions()
    {
        return $this->hasMany(Extension::class, ['owner_id' => 'id']);
    }

    public function getRankLink()
    {
        $class='user-rank-link';

        if($this->rank<=5)
            $class="{$class} gold";
        elseif($this->rank<=20)
            $class="{$class} silver";
        elseif($this->rank<=50)
            $class="{$class} bronze";

        return Html::a(Html::encode($this->display_name), ['user/view', 'id' => $this->id], array('class'=>$class));
    }

    public function getForumUrl()
    {
        return '@web/forum/index.php?showuser=' . urlencode($this->id);
    }

    /**
     * @return ActiveQuery
     */
    private static function findTopUsers()
    {
        return static::find()
            // active within the last 100 days
            ->where(['>', 'login_time', date('Y-m-d 00:00:00', strtotime('now - 100 days'))])
            ->orderBy(['rating' => SORT_DESC])
            ->limit(20);
    }

    public static function getTopUsers()
    {
        return static::findTopUsers()->all();
    }

    public static function getTopExtensionAuthors()
    {
        return static::findTopUsers()
            ->andWhere('extension_count > 0')
            ->orderBy(['extension_count' => SORT_DESC])->all();
    }

    public static function getTopWikiAuthors()
    {
        return static::findTopUsers()
            ->andWhere('wiki_count > 0')
            ->orderBy(['wiki_count' => SORT_DESC])->all();
    }

    public static function getTopCommentAuthors()
    {
        return static::findTopUsers()
            ->andWhere('comment_count > 0')
            ->orderBy(['comment_count' => SORT_DESC])->all();
    }

    public function getBadges()
    {
        return $this->hasMany(UserBadge::class, ['user_id' => 'id']);
    }

    public function getAuthClients()
    {
        return $this->hasMany(Auth::class, ['user_id' => 'id']);
    }

    /**
     * @return array url to this object. Should be something to be passed to [[\yii\helpers\Url::to()]].
     */
    public function getUrl($action = 'view', $params = [])
    {
        if ($action !== 'profile') {
            $params['id'] = $this->id;
        }
        $url = ["user/$action"];
        return empty($params) ? $url : array_merge($url, $params);
    }

    /**
     * @return null|string github account if attached
     */
    public function getGithub()
    {
        foreach ($this->authClients as $client) {
            if ($client->source === 'github') {
                return $client->source_login;
            }
        }
        return null;
    }

    /**
     * Finds user by email verification token
     * @param string $token
     * @return self
     */
    public static function findByEmailVerificationToken($token)
    {
        $user = static::findOne([
            'email_verification_token' => $token,
            'email_verified' => false,
            'status' => self::STATUS_ACTIVE,
        ]);

        if (!$user) {
            return null;
        }

        if (!static::isEmailVerificationTokenValid($token)) {
            return null;
        }

        return $user;
    }

    /**
     * Generates new email verification token
     */
    public function generateEmailVerificationToken()
    {
        $this->email_verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Validate email verification token
     */
    public static function validateEmailVerificationToken($token)
    {
        $user = static::findByEmailVerificationToken($token);
        if ($user) {
            $user->email_verified = true;
            $user->email_verification_token = null;
            return $user->save();
        }
        return false;
    }

    /**
     * Finds out if email verification token is valid
     *
     * @param string $token email verification token
     * @return boolean
     */
    public static function isEmailVerificationTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.emailVerificationTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }
}
