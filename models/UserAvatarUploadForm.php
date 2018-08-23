<?php

namespace app\models;

use Yii;
use yii\imagine\Image;
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
            ['avatar', 'file',
                'extensions' => ['png','jpg','jpeg'],
                'maxSize' => 4096 * 1024, // 4MB
            ],
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

            try {
                $avatarPath = $this->user->getAvatarPath();
                FileHelper::createDirectory(dirname($avatarPath));
                $this->avatar->saveAs("$avatarPath.orig");
                Image::thumbnail("$avatarPath.orig", 200, 200)->save($avatarPath);
                return true;
            } catch (\Throwable $e) {
                Yii::error($e);
                $this->addError('avatar', 'Unable to process image.');
                if (file_exists("$avatarPath.orig")) {
                    unlink("$avatarPath.orig");
                }
            }
        }

        return false;
    }
}