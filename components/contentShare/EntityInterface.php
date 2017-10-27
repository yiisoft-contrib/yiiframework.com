<?php

namespace app\components\contentShare;

interface EntityInterface
{
    /**
     * @return string
     */
    public function getContentShareObjectTypeId();

    /**
     * @return int
     */
    public function getContentShareObjectId();

    /**
     * Return the message for twitter.
     *
     * If you do not need to publishing the message then return false.
     *
     * @return bool|string
     */
    public function getContentShareTwitterMessage();
}
