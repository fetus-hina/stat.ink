<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class UserStatNawabariAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'user-stat-nawabari-inked.js',
        'user-stat-nawabari-wp.js',
    ];
    public $depends = [
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotResizeAsset::class,
        JqueryAsset::class,
        NumberFormatAsset::class,
    ];
}
