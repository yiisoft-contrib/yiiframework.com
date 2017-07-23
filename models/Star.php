<?php

namespace app\models;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "star".
 *
 * @property integer $user_id
 * @property string $object_type
 * @property integer $object_id
 * @property integer $star
 * @property string $created_at
 */
class Star extends ActiveRecord
{
    /**
     * @var array Allow class for star
     */
    public static $modelClasses = ['Wiki', 'Extension'];

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
     * @param ActiveRecord $model the specified model
     * @return int the star counts
     */
    public static function getStarCount($model)
    {
        return static::find()
            ->where(['object_type' => $model->formName(), 'object_id' => (int)$model->primaryKey])
            ->sum('star');
    }

    /**
     * Casts a star to the specified content object.
     * @param ActiveRecord $model the type of the content object
     * @param integer $userID the user ID
     * @param integer $starValue the star value (1 means star, 0 means unstar, -1 means toggle)
     * @return int the updated star information of the content object. False if the content object is invalid.
     */
    public static function castStar($model, $userID, $starValue = -1)
    {
        /** @var $star Star */
        $star = static::findOne([
            'object_type' => $model->formName(),
            'object_id' => (int)$model->primaryKey,
            'user_id' => $userID,
        ]);

        if ($star === null)
        {
            $star = new static;
            $star->object_type = $model->formName();
            $star->object_id = (int)$model->primaryKey;
            $star->user_id = $userID;
            $star->star = 1;
            $star->save(false);
        }
        else
        {
            if(-1==$starValue)
            {
                $star->star = (0==$star->star) ? 1 : 0;
                $star->save(false);
            }
            else
            {
                /*
                 * If record already exist, allow only unstar (no automatic starring)
                 */
                if($starValue==0 && $star->star!=$starValue)
                {
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
        $models=[];
        foreach(self::$modelClasses as $class)
        {
            /** @var $modelClass ActiveRecord */
            $modelClass = "app\\models\\$class";

            $ids = static::find()
                ->select('object_id')
                ->where(['user_id' => $userID, 'object_type' => $class, 'star' => 1])
                ->column();

            $models = array_merge(
                $modelClass::find()->active()->andWhere(['id' => $ids])->all(),
                $models
            );
        }
        return $models;
    }

    /**
     * @param ActiveRecord $model
     * @return ActiveQuery
     */
    public static function getFollowers($model)
    {
        $class = $model->formName();
        return User::find()->where([
            'id' => Star::find()
                ->select('user_id')
                ->where(['star' => 1, 'object_type' => $class, 'object_id' => (int)$model->primaryKey])
        ]);
    }

    /**
     * Return current follower count
     *
     * @param ActiveRecord $model
     *
     * @return int
     */
    public static function getFollowerCount($model)
    {
        return static::find()->where([
            'object_type' => $model->formName(),
            'object_id' => (int) $model->primaryKey,
        ])->count();
    }

    /**
     * Return current star value
     *
     * @param ActiveRecord $model
     * @param $userID
     *
     * @return int
     */
    public static function getStarValue($model, $userID)
    {
        /** @var $star Star */
        $star = static::findOne([
            'object_type' => $model->formName(),
            'object_id' => (int) $model->primaryKey,
            'user_id' => $userID,
        ]);

        return $star === null ? 0 : $star->star;
    }
}
