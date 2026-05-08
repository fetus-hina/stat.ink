<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

final class CreateBattle3Asset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    /**
     * @var string[]
     */
    public $js = [
        'create-battle3.js',
    ];

    /**
     * @var string[]
     * @phpstan-var class-string<AssetBundle>[]
     */
    public $depends = [
        BootstrapPluginAsset::class,
        YiiAsset::class,
    ];
}
