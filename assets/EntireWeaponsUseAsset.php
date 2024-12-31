<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotStackAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireWeaponsUseAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'weapons-use.js',
    ];
    public $depends = [
        AppAsset::class,
        FlotAsset::class,
        FlotIconAsset::class,
        FlotStackAsset::class,
        FlotTimeAsset::class,
        JqueryAsset::class,
    ];
}
