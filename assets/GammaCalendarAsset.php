<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class GammaCalendarAsset extends AssetBundle
{
    public $sourcePath = '@bower/gammacalendar/dist';
    public $js = [
        'gammacalendar-v0.0.1.min.js',
    ];
    public $css = [
        'gammacalendar-v0.0.1.min.css',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
}
