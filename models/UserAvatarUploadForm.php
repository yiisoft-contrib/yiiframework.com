<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class UserAvatarUploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $avatar;
    /**
     * @var User
     */
    public $user;


    public function init()
    {
        if ($this->user === null) {
            throw new InvalidConfigException(__CLASS__ . '::$user is not configured!');
        }
        parent::init();
    }

    public function rules()
    {
        return [
            ['avatar', 'required'],
            ['avatar', 'file', 'extensions' => ['png','jpg']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'avatar' => 'Upload profile picture:'
        ];
    }

    public function getMaxFileSize()
    {
        /** @var $fileValidator FileValidator[] */
        $fileValidator = array_filter($this->getValidators()->getArrayCopy(), function($v) { return $v instanceof FileValidator; });
        return reset($fileValidator)->getSizeLimit();
    }

    public function upload()
    {
        if ($this->validate()) {
            // TODO crop image
            FileHelper::createDirectory(dirname($this->user->getAvatarPath()));
            $this->avatar->saveAs($this->user->getAvatarPath());
            return true;
        }

        return false;
    }
}