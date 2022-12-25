<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use app\assets\ChartJsAsset;
use app\assets\ColorSchemeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class EntireXpowerDistrib3HistogramAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'entire-xpower-distrib3-histogram.js',
    ];
    public $depends = [
        ChartJsAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
    ];
}
