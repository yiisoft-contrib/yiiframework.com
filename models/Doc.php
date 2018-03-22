<?php

namespace app\models;

use app\components\object\ClassType;
use app\components\object\ObjectIdentityInterface;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%doc}}".
 *
 * @property integer $id
 * @property string $object_type
 * @property string $object_key
 * @property string $url
 * @property string $title
 * @property integer $created_at
 */
class Doc extends ActiveRecord implements Linkable, ObjectIdentityInterface
{
    /**
     * @var string[] Available object types for comments.
     */
    public static $availableObjectTypes = [ClassType::GUIDE, ClassType::API];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%doc}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_type', 'object_key'], 'required'],
            [['object_type', 'object_key', 'title', 'url'], 'string', 'max' => 255],
//            ['url', 'required'],
            [['title', 'url'], 'trim'],
            ['object_type', 'in', 'range' => static::$availableObjectTypes],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_type' => 'Object Type',
            'object_key' => 'Object Key'
        ];
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return ClassType::DOC;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->id;
    }

    /**
     * @param string $objectType
     * @param string $objectKey
     * @param string $url
     * @param string $title
     *
     * @return bool|static
     */
    public static function getObject($objectType, $objectKey, $url, $title)
    {
        if (!in_array($objectType, static::$availableObjectTypes, true)) {
            return false;
        }

        $doc = static::findOne([
            'object_type' => $objectType,
            'object_key' => $objectKey
        ]);

        if ($doc) {
            $doc->url = $url;
            $doc->title = $title;
            if ($doc->isAttributeChanged('url') || $doc->isAttributeChanged('title')) {
                $doc->save(false);
            }

            return $doc;
        }

        try {
            $doc = new static();
            $doc->loadDefaultValues();

            $doc->object_type = $objectType;
            $doc->object_key = $objectKey;
            $doc->url = $url;
            $doc->title = $title;

            if ($doc->save()) {
                return $doc;
            }
        } catch (\yii\db\Exception $ex) {
            $doc = static::findOne([
                'object_type' => $objectType,
                'object_key' => $objectKey
            ]);

            if ($doc) {
                return $doc;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getUrl($action = 'view', $params = [])
    {
        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function getLinkTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            Comment::deleteAll(['object_type' => $this->getObjectType(), 'object_id' => $this->getObjectId()]);
            Star::deleteAll(['object_type' => $this->getObjectType(), 'object_id' => $this->getObjectId()]);

            return true;
        }

        return false;
    }

}
