<?php

namespace app\apidoc\assets;

use yii\web\View;

/**
 * The asset bundle for the offline template.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class AssetBundle extends \yii\web\AssetBundle
{
    public $sourcePath = '@app/apidoc/assets/css';
    public $css = [
//		'api.css',
        'style.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
