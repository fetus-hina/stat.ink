<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\jqueryColor\JqueryColorAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class KillRatioColumnAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'kill-ratio-column.js',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        JqueryAsset::class,
        JqueryColorAsset::class,
    ];
}
