<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class FlotAsset extends AssetBundle
{
    public $sourcePath = '@bower/flot';
    public $js = [
        'jquery.flot.js',
        'jquery.flot.stack.js',
        'jquery.flot.time.js',
        'jquery.flot.resize.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\TimezoneJsAsset',
    ];
}
