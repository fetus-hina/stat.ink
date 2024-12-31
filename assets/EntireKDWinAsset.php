<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\jqueryColor\JqueryColorAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireKDWinAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'kd-win.css',
    ];
    public $js = [
        'kd-win.js',
    ];
    public $depends = [
        BootstrapAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
        JqueryColorAsset::class,
    ];
}
