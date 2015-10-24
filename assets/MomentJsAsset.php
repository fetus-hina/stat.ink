<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class MomentJsAsset extends AssetBundle
{
    public $sourcePath = '@bower/moment/min';
    public $js = [
        'moment.min.js',
    ];
}
