<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SessionCalendarAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'session-calendar.js',
    ];
    public $depends = [
        CalHeatmapAsset::class,
        JqueryAsset::class,
        AppAsset::class,
    ];
}
