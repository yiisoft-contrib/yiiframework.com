<?php

namespace app\components\contentShare\services;

use app\components\contentShare\EntityInterface;
use app\models\ContentShare;
use yii\base\BaseObject;

/**
 * @property ContentShare $contentShare
 */
abstract class BaseService extends BaseObject
{
    /**
     * @var ContentShare
     */
    private $_contentShare;

    public function __construct(ContentShare $contentShare, array $config = [])
    {
        $this->_contentShare = $contentShare;

        parent::__construct($config);
    }

    /**
     * @return bool
     */
    abstract public function publish();

    /**
     * Return the message for publication.
     *
     * If you do not need to publishing the message then return false.
     *
     * @param EntityInterface $entity
     *
     * @return bool|string
     */
    abstract public function getMessage(EntityInterface $entity);

    /**
     * @return ContentShare
     */
    public function getContentShare()
    {
        return $this->_contentShare;
    }
}
