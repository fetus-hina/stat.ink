<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class TimezoneJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/timezone-js/src';
    public $js = [
        'date.js',
    ];
    public $depends = [
        'app\assets\TimezoneDataAsset',
    ];
}
