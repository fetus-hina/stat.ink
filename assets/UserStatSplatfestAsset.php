<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\jqueryColor\JqueryColorAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class UserStatSplatfestAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'user-stat-splatfest.js',
    ];
    public $depends = [
        ChartJsAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
        JqueryColorAsset::class,
        LuxonAsset::class,
    ];
}
