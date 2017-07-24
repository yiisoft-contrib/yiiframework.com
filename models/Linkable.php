<?php

namespace app\models;

interface Linkable
{
    /**
     * @return array url to this object. Should be something to be passed to [[\yii\helpers\Url::to()]].
     */
    public function getUrl($action = 'view', $params = []);

    /**
     * @return string title to display for a link to this object.
     */
    public function getLinkTitle();

    /**
     * @return string the type of this object, e.g. News, Extension, Wiki
     */
    public function getItemType();
}
