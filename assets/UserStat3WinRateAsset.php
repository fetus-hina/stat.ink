<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class UserStat3WinRateAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'v3-user-stats-win-rate.js',
    ];
    public $depends = [
        ChartJsAsset::class,
        ChartJsDataLabelsAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
        PatternomalyAsset::class,
    ];
}
