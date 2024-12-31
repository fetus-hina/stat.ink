<?php

/**
 * @copyright Copyright (C) 2021-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class ChartJsDataLabelsAsset extends AssetBundle
{
    public $sourcePath = '@npm/chartjs-plugin-datalabels/dist';
    public $js = [
        'chartjs-plugin-datalabels.min.js',
    ];
    public $depends = [
        ChartJsAsset::class,
    ];
}
