<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class EntireSalmon3TideAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'entire-salmon3-tide-tide.js',
        'entire-salmon3-tide-event.js',
    ];
    public $depends = [
        ChartJsAsset::class,
        ChartJsDataLabelsAsset::class,
        ColorSchemeAsset::class,
        JqueryAsset::class,
        PatternomalyAsset::class,
    ];
}
