<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class TimezoneDataAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/tz-data';
    public $js = [
        'tz-init.js',
    ];
}
