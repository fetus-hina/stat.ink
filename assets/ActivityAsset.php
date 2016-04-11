<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class ActivityAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/activity';
    public $js = [
        'activity.js',
    ];
    public $depends = [
        CalHeatmapAsset::class,
        JqueryAsset::class,
    ];
}
