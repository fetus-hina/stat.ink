<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ChartJsErrorBarsAsset extends AssetBundle
{
    public $sourcePath = '@npm/chartjs-chart-error-bars/build';
    public $js = [
        'index.umd.min.js',
    ];
    public $depends = [
        ChartJsAsset::class,
    ];
}
