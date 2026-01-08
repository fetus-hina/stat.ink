<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ChartJsLuxonAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@npm/chartjs-adapter-luxon/dist';

    /**
     * @var string[]
     */
    public $js = [
        'chartjs-adapter-luxon.umd.min.js',
    ];

    /**
     * @var AssetBundle[]
     */
    public $depends = [
        ChartJsAsset::class,
        LuxonAsset::class,
    ];
}
