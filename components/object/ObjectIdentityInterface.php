<?php

namespace app\components\object;

interface ObjectIdentityInterface
{
    /**
     * @return string
     */
    public function getObjectType();

    /**
     * @return int
     */
    public function getObjectId();
}
