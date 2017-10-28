<?php

namespace app\components\contentShare;

use app\components\objectKey\ObjectKeyInterface;

interface EntityInterface extends ObjectKeyInterface
{
    /**
     * Return the message for twitter.
     *
     * If you do not need to publishing the message then return false.
     *
     * @return bool|string
     */
    public function getContentShareTwitterMessage();
}
