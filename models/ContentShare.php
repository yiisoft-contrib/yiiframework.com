<?php

namespace app\models;

use app\components\contentShare\EntityInterface;
use app\components\contentShare\services\BaseService;
use app\components\contentShare\services\TwitterService;
use app\components\object\ClassType;
use app\jobs\ContentShareJob;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%content_share}}".
 *
 * @property integer $id
 * @property integer $object_type_id
 * @property integer $object_id
 * @property integer $service_id
 * @property integer $status_id
 * @property string $message
 * @property string $created_at
 * @property string $posted_at
 *
 * @property BaseService $service
 */
class ContentShare extends ActiveRecord
{
    const STATUS_NEW = 10;
    const STATUS_PUBLISHED = 20;
    const STATUS_FAILED = 30;

    const SERVICE_TWITTER = 'twitter';

    /**
     * @var BaseService
     */
    private $_service;

    public static $serviceClasses = [
        ContentShare::SERVICE_TWITTER => TwitterService::class
    ];

    public static $availableObjectTypeIds = [ClassType::NEWS, ClassType::WIKI, ClassType::EXTENSION];
    public static $availableServiceIds = [self::SERVICE_TWITTER];

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
            [['object_type_id', 'object_id', 'service_id', 'status_id', 'message'], 'required'],
            [['object_id', 'status_id'], 'integer'],
            [['object_type_id', 'service_id', 'message'], 'string'],

            ['object_type_id', 'unique', 'targetAttribute' => ['object_type_id', 'object_id', 'service_id']],

            ['object_type_id', 'in', 'range' => static::$availableObjectTypeIds],
            ['service_id', 'in', 'range' => static::$availableServiceIds]
        ];
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
     * @param EntityInterface $entity
     *
     * @param bool $forceReCreate
     */
    public static function addJobs(EntityInterface $entity, $forceReCreate = false)
    {
        /** @var ContentShare[] $listOfExistingContentShare */
        $listOfExistingContentShare = static::find()
            ->andWhere([
                'object_type_id' => $entity->getObjectType(),
                'object_id' => $entity->getObjectId(),
                'service_id' => static::$availableServiceIds
            ])
            ->indexBy('service_id')
            ->all();

        $newListContentShare = [];
        foreach (array_diff(static::$availableServiceIds, array_keys($listOfExistingContentShare)) as $serviceId) {
            $contentShare = new static();
            $contentShare->loadDefaultValues();

            $contentShare->object_type_id = $entity->getObjectType();
            $contentShare->object_id = $entity->getObjectId();
            $contentShare->service_id = $serviceId;

            $message = $contentShare->service->getMessage($entity);
            if ($message === false) {
                continue;
            }
            $contentShare->message = $message;

            if ($contentShare->save()) {
                $newListContentShare[$serviceId] = $contentShare;
            } else {
                Yii::error('Failed creating contentShare to add to the queue: ' . json_encode($contentShare->getErrors()));
            }
        }

        $listContentShare = $forceReCreate === true ? array_merge($listOfExistingContentShare, $newListContentShare) : $newListContentShare;
        foreach ($listContentShare as $contentShare) {
            Yii::$app->queue->push(new ContentShareJob(['contentShareId' => $contentShare->id]));
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->status_id === null) {
                $this->status_id = self::STATUS_NEW;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     *
     * @return ContentShareQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContentShareQuery(static::class);
    }

    /**
     * @return BaseService|object
     * @throws Exception
     */
    public function getService()
    {
        if ($this->_service === null) {
            if (!array_key_exists($this->service_id, static::$serviceClasses)) {
                throw new Exception("Service {$this->service_id} not exists.");
            }

            $this->_service = Yii::createObject(static::$serviceClasses[$this->service_id], [$this]);
        }

        return $this->_service;
    }

    /**
     * @return bool
     */
    public function publish()
    {
        if ($this->service->publish()) {
            $this->status_id = self::STATUS_PUBLISHED;
            $this->posted_at = date('Y-m-d H:i:s');
        } else {
            $this->status_id = self::STATUS_FAILED;
        }

        if (!$this->save()) {
            Yii::error("Failed saving contentShare id = {$this->id} after publishing.");
        }

        return (int)$this->status_id === self::STATUS_PUBLISHED;
    }
}
