<?php

namespace app\apidoc\assets;

use yii\web\View;

/**
 * The asset bundle for the offline template.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class JsSearchAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/cebe/js-search';
    public $js = [
        'jssearch.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
