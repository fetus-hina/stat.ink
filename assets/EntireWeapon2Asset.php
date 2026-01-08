<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotErrorbarsAsset;
use jp3cki\yii2\flot\FlotPieAsset;
use jp3cki\yii2\flot\FlotTimeAsset;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class EntireWeapon2Asset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'weapon2.js',
    ];
    public $depends = [
        BabelPolyfillAsset::class,
        ColorSchemeAsset::class,
        FlotAsset::class,
        FlotErrorbarsAsset::class,
        FlotPieAsset::class,
        FlotTimeAsset::class,
        JqueryAsset::class,
        SortableTableAsset::class,
        StatByMapRuleAsset::class,
        TableResponsiveForceAsset::class,
    ];
}
