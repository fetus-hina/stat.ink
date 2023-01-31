<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

final class BattleEditAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    /**
     * @var string[]
     */
    public $js = [
        'battle-edit.js',
    ];

    /**
     * @var string[]
     * @phpstan-var class-string<AssetBundle>[]
     */
    public $depends = [
        YiiAsset::class,
    ];
}
