<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class SimpleWinLosePieAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'simple-win-lose-pie.js',
    ];
    public $depends = [
        ChartJsAsset::class,
        ChartJsDataLabelsAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
    ];
}
