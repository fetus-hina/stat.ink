<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class FestPowerHistoryAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'fest-power-history.css',
    ];
    public $js = [
        'fest-power-history.js',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotResizeAsset::class,
        JqueryAsset::class,
        NumberFormatAsset::class,
    ];
}
