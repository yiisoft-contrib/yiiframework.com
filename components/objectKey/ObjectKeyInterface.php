<?php

namespace app\components;

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
