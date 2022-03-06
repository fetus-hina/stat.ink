<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireWeaponAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'entire-weapon-based-on-k-or-d.js',
        'entire-weapon-kd-stats.js',
        'entire-weapon-kd-summary.js',
        'entire-weapon-stage.js',
        'entire-weapon-usepct.js',
    ];
    public $depends = [
        BabelPolyfillAsset::class,
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotPieAsset::class,
        FlotStackAsset::class,
        FlotTimeAsset::class,
        IntlPolyfillAsset::class,
        JqueryAsset::class,
    ];
}
