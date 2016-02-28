<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class CalHeatmapAsset extends AssetBundle
{
    public $sourcePath = '@bower/cal-heatmap';
    public $js = [
        'cal-heatmap.js',
    ];
    public $css = [
        'cal-heatmap.css',
    ];
    public $depends = [
        D3Asset::class,
    ];
}
