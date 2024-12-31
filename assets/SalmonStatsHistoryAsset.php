<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SalmonStatsHistoryAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'salmon-stats-history.js',
    ];
    public $depends = [
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotTimeAsset::class,
        IntlPolyfillAsset::class,
        JqueryAsset::class,
    ];
}
