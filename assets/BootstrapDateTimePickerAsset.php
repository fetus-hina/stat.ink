<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class BootstrapDateTimePickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/eonasdan-bootstrap-datetimepicker/build';
    public $js = [
        'js/bootstrap-datetimepicker.min.js',
    ];
    public $css = [
        'css/bootstrap-datetimepicker.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'jp3cki\yii2\momentjs\MomentJsAsset',
        'jp3cki\yii2\momentjs\MomentJsLocaleAsset',
    ];
}
