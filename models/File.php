<?php

namespace app\models;

use creocoder\flysystem\Filesystem;
use League\Flysystem\FileNotFoundException;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveQuery;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property string $object_type
 * @property integer $object_id
 * @property integer $upload_time
 * @property string $file_name
 * @property integer $file_size
 * @property string $mime_type
 * @property integer $download_count
 * @property string $summary
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $creator
 */
class File extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @return FileQuery
     */
    public static function find()
    {
        return Yii::createObject(FileQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
            [
                 'class' => BlameableBehavior::className(),
                 'createdByAttribute' => 'created_by',
                 'updatedByAttribute' => false,
             ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['summary', 'string'],
            ['file_name', 'required'],
            ['file_name', 'file',
                'extensions' => 'gif, png, jpg, jpeg, bmp, zip, gz, tgz, bz2',
                'maxSize' => 2097152, // 2MB
            ],
            ['file_name', 'unique',
                'filter' => function($query) {
                    /** @var $query ActiveQuery */
                    // filename must be unique per type
                    $query->andWhere(['object_type' => $this->object_type, 'object_id' => $this->object_id]);
                },
                'message' => 'A file with this name does already exist.'
            ],
        ];
    }

    /**
     * @return string the file extension name (e.g. 'jpg', 'gif')
     */
    public function getExtension()
    {
        if (($pos = strrpos($this->file_name, '.')) !== false) {
            return strtolower(substr($this->file_name, $pos + 1));
        } else {
            return '';
        }
    }

    /**
     * @return string the absolute file path
     */
    public function getPath()
    {
        return 'files/' . static::generatePath($this->object_type, $this->object_id) . '/' . $this->id . '.' . $this->extension;
    }

    /**
     * Generates the relative path that will store files associated with an object.
     * For example, for News 1234, the generated path would be 'news/123/4'.
     * @param string $objectType object type (e.g. 'News')
     * @param integer $objectID object ID
     * @return string the generated relative path
     */
    public static function generatePath($objectType, $objectID)
    {
        return strtolower($objectType) . '/' . implode('/', preg_split('/(\d{3})/', (string)$objectID, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY));
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->file_name instanceof UploadedFile) {
                $this->file_size = $this->file_name->size;
                $this->mime_type = FileHelper::getMimeTypeByExtension($this->file_name->name);
                if ($this->mime_type === null) {
                    $this->mime_type = 'text/plain';
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($this->file_name instanceof UploadedFile) {
            /** @var $fs Filesystem */
            $fs = Yii::$app->fs;
            $fp = fopen($this->file_name->tempName, 'rb');
            $fs->writeStream($this->path, $fp);
            if (is_resource($fp)) {
                fclose($fp);
            }
        }
    }

    public function afterDelete()
    {
        /** @var $fs Filesystem */
        $fs = Yii::$app->fs;
        $fs->delete($this->path);
    }

    /**
     * Saves a file based on an uploaded file and the content object associated with this file.
     * @param UploadedFile $upload the uploaded file.
     * @param File the File object associated with the content object. If null, a new File object will be created.
     * @param ActiveRecord the content object that the file is associated with
     * @param string the name of the attribute that stores the ID of the related file object.
     * If null, it means no need to store the file ID.
     * @return boolean whether the saving is successful
     */
    public function upload($upload,$file,$object,$attribute=null)
    {
        if(!$upload instanceof UploadedFile || $upload->hasError)
            return false;

        if($file===null)
            $file=new File;

        $file->object_type=get_class($object);
        $file->object_id=$object->id;
        $file->file_name=$upload;
        if(trim($file->summary)==='')
            $file->summary=$upload->name;

        if(!$file->save())
            return false;

        if($attribute!==null && $object->$attribute!=$file->id)
        {
            $object->$attribute=$file->id;
            $object->updateByPk($object->primaryKey,array($attribute=>$file->id));
        }
        return true;
    }

    /**
     * @return Response
     * @throws FileNotFoundException if the file does not exist.
     */
    public function download()
    {
        /** @var $fs Filesystem */
        $fs = Yii::$app->fs;
        $this->updateCounters(['download_count' => 1]);
        return Yii::$app->response->sendStreamAsFile($fs->readStream($this->path), $this->file_name, [
            'mimeType' => $this->mime_type,
        ]);
    }

    /*
    public function getImageTag($size=array(0,0),$cropping=true)
    {
        if($cropping)
        {
            list($width,$height)=$size;
            $width or ($width=null);
            $height or ($height=null);
        }
        else
            $width=$height=null;
        return img($this->getImageUrl($size),$this->file_name,$width,$height);
    }

    public function getImageUrl($size=array(0,0),$cropping=true)
    {
        $prefix=self::generatePath($this->object_type, $this->object_id);
        $params=array(
            $this->id,
            $this->upload_time,
            $size,
            $cropping,
        );
        $fileName=sprintf('%x',crc32(serialize($params))).'.'.$this->extension;
        $basePath=Yii::getPathOfAlias(param('temp.image.dir'));
        $targetFile=$basePath.DS.$prefix.DS.$fileName;
        if(!is_file($targetFile))
        {
            @mkdir($basePath.DS.$prefix, 0777, true);
            $sourceFile=$this->path;
            if(is_file($sourceFile))
                ImageHelper::resize($sourceFile,$targetFile,$size,$cropping);
            else
            {
                // the file system doesn't contain an image that is recorded in the database
                // this should not happen on production server
                return '#';
            }
        }
        return bu(param('temp.image.url'))."/$prefix/$fileName";
    }
*/

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
}
