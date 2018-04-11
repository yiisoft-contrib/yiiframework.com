<?php

namespace app\models;

use app\components\object\ClassType;
use app\components\object\ObjectIdentityInterface;
use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "comment".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $object_type
 * @property string $object_id
 * @property string $text
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property int $total_votes
 * @property int $up_votes
 * @property float $rating
 *
 * @property User $user
 */
class Comment extends ActiveRecord implements ObjectIdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @var string[] Available object types for comments.
     */
    public static $availableObjectTypes = [ClassType::WIKI, ClassType::EXTENSION, ClassType::DOC];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @return CommentQuery
     */
    public static function find()
    {
        return Yii::createObject(CommentQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
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
            [['text'], 'required'],
            [['text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'text' => Yii::t('app', 'Text'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveRecord
     */
    public function getModel()
    {
        if (!in_array($this->object_type, static::$availableObjectTypes, true)) {
            return null;
        }

        /** @var ActiveRecord $modelClass */
        $modelClass = ClassType::getClass($this->object_type);
        return $modelClass::findOne($this->object_id);
    }

    /**
     * @return array
     */
    public function getUrl()
    {
        /** @var $model Linkable */
        $model = $this->getModel();
        if (!$this->model instanceof Linkable) {
            return null;
        }

        $url = $model->getUrl();
        if (is_array($url)) {
            $url['#'] = "c{$this->id}";
            return $url;
        }
        return "$url#c{$this->id}";
    }

    /**
     * @inheritdoc
     */
    public function getObjectType()
    {
        return ClassType::COMMENT;
    }

    /**
     * @inheritdoc
     */
    public function getObjectId()
    {
        return $this->id;
    }
}
