<?php

/**
 * @copyright Copyright (C) 2020-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use statink\yii2\momentjs\MomentAsset;
use statink\yii2\momentjs\MomentTimezoneAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class LeaguePowerHistoryAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'league-power-history.css',
    ];
    public $js = [
        'league-power-history.js',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotResizeAsset::class,
        FlotTimeAsset::class,
        JqueryAsset::class,
        MomentAsset::class,
        MomentTimezoneAsset::class,
    ];
}
