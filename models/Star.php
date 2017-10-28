<?php

namespace app\models;

use app\components\objectKey\ObjectKeyHelper;
use app\components\objectKey\ObjectKeyInterface;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "star".
 *
 * @property integer $user_id
 * @property string $object_type
 * @property integer $object_id
 * @property integer $star
 * @property string $created_at
 */
class Star extends ActiveRecord implements ObjectKeyInterface
{
    /**
     * @var string[] Available object types for stars.
     */
    public static $availableObjectTypes = [ObjectKeyHelper::TYPE_WIKI, ObjectKeyHelper::TYPE_EXTENSION];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%star}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(false),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'object_type', 'object_id', 'created_at'], 'required'],
            [['user_id', 'object_id', 'star'], 'integer'],
            [['created_at'], 'safe'],
            [['object_type'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'object_type' => 'Object Type',
            'object_id' => 'Object ID',
            'star' => 'Star',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Returns the total stars for the specified model (how many users stared the object).
     * @param ObjectKeyInterface|ActiveRecord $model the specified model
     *
     * @return int the star counts
     */
    public static function getStarCount(ObjectKeyInterface $model)
    {
        return static::find()
            ->where(['object_type' => $model->getObjectType(), 'object_id' => $model->getObjectId()])
            ->sum('star');
    }

    /**
     * Casts a star to the specified content object.
     * @param ActiveRecord|ObjectKeyInterface $model the type of the content object
     * @param integer $userID the user ID
     * @param integer $starValue the star value (1 means star, 0 means unstar, -1 means toggle)
     * @return int the updated star information of the content object. False if the content object is invalid.
     */
    public static function castStar($model, $userID, $starValue = -1)
    {
        /** @var $star Star */
        $star = static::findOne([
            'object_type' => $model->getObjectType(),
            'object_id' => $model->getObjectId(),
            'user_id' => $userID,
        ]);

        if ($star === null) {
            $star = new static;
            $star->object_type = $model->getObjectType();
            $star->object_id = $model->getObjectId();
            $star->user_id = $userID;
            $star->star = 1;
            $star->save(false);
        } else {
            if (-1 == $starValue) {
                $star->star = (0 == $star->star) ? 1 : 0;
                $star->save(false);
            } else {
                /*
                 * If record already exist, allow only unstar (no automatic starring)
                 */
                if ($starValue == 0 && $star->star != $starValue) {
                    $star->star = $starValue;
                    $star->save(false);
                }
            }
        }

        return $star->star;
    }

    /**
     * Returns the target models that the specified user is following
     * @param integer $userID the user ID
     * @return array list of models that the user is following
     */
    public static function getTargets($userID)
    {
        $models = [];
        foreach (static::$availableObjectTypes as $objectType) {
            /** @var ActiveRecord $modelClass */
            $modelClass = ObjectKeyHelper::getClass($objectType);

            $ids = static::find()
                ->select('object_id')
                ->where(['user_id' => $userID, 'object_type' => $objectType, 'star' => 1])
                ->column();

            $models = array_merge(
                $modelClass::find()->active()->andWhere(['id' => $ids])->all(),
                $models
            );
        }
        ArrayHelper::multisort($models, ['itemType', 'linkTitle']);
        return $models;
    }

    /**
     * @param ObjectKeyInterface $model
     * @return ActiveQuery
     */
    public static function getFollowers($model)
    {
        return User::find()->active()->andWhere([
            'id' => Star::find()
                ->select('user_id')
                ->where(['star' => 1, 'object_type' => $model->getObjectType(), 'object_id' => $model->getObjectId()])
        ]);
    }

    /**
     * Return current follower count
     *
     * @param ObjectKeyInterface $model
     *
     * @return int
     */
    public static function getFollowerCount($model)
    {
        return static::getFollowers($model)->count();
    }

    /**
     * Return current star value
     *
     * @param ObjectKeyInterface $model
     * @param $userID
     *
     * @return int
     */
    public static function getStarValue($model, $userID)
    {
        /** @var $star Star */
        $star = static::findOne([
            'object_type' => $model->getObjectType(),
            'object_id' => $model->getObjectId(),
            'user_id' => $userID,
        ]);

        return $star === null ? 0 : $star->star;
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->object_type;
    }

    /**
     * @return int
     */
    public function getObjectId()
    {
        return $this->object_id;
    }
}
