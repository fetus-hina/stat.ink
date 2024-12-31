<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireKnockoutAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'knockout.js',
    ];
    public $css = [
        'knockout.css',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotPieAsset::class,
        FlotResizeAsset::class,
        JqueryAsset::class,
    ];
}
