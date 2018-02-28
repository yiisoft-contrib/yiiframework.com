<?php

namespace app\components\contentShare;

use app\components\object\ObjectIdentityInterface;

interface EntityInterface extends ObjectIdentityInterface
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
