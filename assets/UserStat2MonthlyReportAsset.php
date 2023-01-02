<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class UserStat2MonthlyReportAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'user-stat-2-monthly-report-pie-winpct.js',
    ];
    public $depends = [
        ChartJsAsset::class,
        ChartJsDataLabelsAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
        PatternomalyAsset::class,
    ];
}
