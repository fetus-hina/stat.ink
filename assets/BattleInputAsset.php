<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

final class BattleInputAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $js = [
        'battle-input-2.js',
    ];
    public $css = [
        'battle-input.css',
    ];
    public $depends = [
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        BowserAsset::class,
        DsegAsset::class,
        JqueryAsset::class,
    ];
}
