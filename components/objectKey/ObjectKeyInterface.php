<?php

namespace app\components\objectKey;

interface ObjectKeyInterface
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
