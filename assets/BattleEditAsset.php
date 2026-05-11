<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

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
     * @var list<string>
     */
    public $js = [
        'battle-edit.js',
    ];

    /**
     * @var list<class-string<AssetBundle>>
     */
    public $depends = [
        ApiFetchAsset::class,
        YiiAsset::class,
    ];
}
