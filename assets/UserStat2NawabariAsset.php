<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use yii\web\AssetBundle;

class UserStat2NawabariAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'user-stat-2-nawabari-inked.js',
        'user-stat-2-nawabari-stats.js',
        'user-stat-2-nawabari-winpct.js',

        // 必ず最後に
        'user-stat-2-nawabari-runner.js',
    ];
    public $depends = [
        AppAsset::class,
        BootstrapIconsAsset::class,
        FlotAsset::class,
    ];
}
