<?php

namespace app\models;

use app\jobs\ContentShareJob;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%content_share}}".
 *
 * @property integer $object_type_id
 * @property integer $object_id
 * @property integer $service_id
 * @property integer $created_at
 */
class ContentShare extends ActiveRecord
{
    const OBJECT_TYPE_NEWS = 1;
    const OBJECT_TYPE_WIKI = 2;
    const OBJECT_TYPE_EXTENSION = 3;

    const SERVICE_TWITTER = 1;

    public static $availableObjectTypeIds = [self::OBJECT_TYPE_NEWS, self::OBJECT_TYPE_WIKI, self::OBJECT_TYPE_EXTENSION];
    public static $availableServiceIds = [self::SERVICE_TWITTER];

    public static $objectTypesData = [
        ContentShare::OBJECT_TYPE_NEWS => [
            'className' => News::class,
            'objectStatusPropertyName' => 'status',
            'objectStatusPublishedId' => News::STATUS_PUBLISHED
        ],
        ContentShare::OBJECT_TYPE_WIKI => [
            'className' => Wiki::class,
            'objectStatusPropertyName' => 'status',
            'objectStatusPublishedId' => Wiki::STATUS_PUBLISHED
        ],
        ContentShare::OBJECT_TYPE_EXTENSION => [
            'className' => Extension::class,
            'objectStatusPropertyName' => 'status',
            'objectStatusPublishedId' => Extension::STATUS_PUBLISHED
        ]
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_share}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_type_id', 'object_id', 'service_id'], 'required'],
            [['object_type_id', 'object_id', 'service_id'], 'integer'],
            ['object_type_id', 'unique', 'targetAttribute' => ['object_type_id', 'object_id', 'service_id']],

            ['object_type_id', 'in', 'range' => static::$availableObjectTypeIds],
            ['service_id', 'in', 'range' => static::$availableServiceIds]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'object_type_id' => Yii::t('app', 'Object type'),
            'object_id' => Yii::t('app', 'Object ID'),
            'service_id' => Yii::t('app', 'Service ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false
            ]
        ];
    }

    /**
     * @param int $objectTypeId
     * @param int $objectId
     * @param int $serviceId
     *
     * @return bool
     */
    public static function exists($objectTypeId, $objectId, $serviceId)
    {
        return static::find()
            ->andWhere([
                'object_type_id' => $objectTypeId,
                'object_id' => $objectId,
                'service_id' => $serviceId
            ])
            ->exists();
    }

    /**
     * @param int $objectTypeId
     * @param int $objectId
     * @param int $serviceId
     *
     * @return bool
     */
    public static function push($objectTypeId, $objectId, $serviceId)
    {
        $contentShare = new static();
        $contentShare->loadDefaultValues();

        $contentShare->object_type_id = $objectTypeId;
        $contentShare->object_id = $objectId;
        $contentShare->service_id = $serviceId;

        return $contentShare->save();
    }

    /**
     * @param int $objectTypeId
     * @param int $objectId
     */
    public static function addJobs($objectTypeId, $objectId)
    {
        $existingServiceIds = static::find()
            ->select('service_id')
            ->andWhere([
                'object_type_id' => $objectTypeId,
                'object_id' => $objectId
            ])
            ->column();

        foreach (array_diff(static::$availableServiceIds, $existingServiceIds) as $serviceId) {
            Yii::$app->queue->push(new ContentShareJob([
                'objectTypeId' => $objectTypeId,
                'objectId' => $objectId,
                'serviceId' => $serviceId,
            ]));
        }
    }
}
