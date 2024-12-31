<?php

/**
 * @copyright Copyright (C) 2020-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use jp3cki\yii2\flot\FlotAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class BattleTimelineAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'battle-smooth.js',
        'battle-timeline.js',
    ];
    public $depends = [
        AppAsset::class,
        FlotAsset::class,
        FlotIconAsset::class,
        Hsv2rgbAsset::class,
        JqueryAsset::class,
        YiiAsset::class,
    ];
}
