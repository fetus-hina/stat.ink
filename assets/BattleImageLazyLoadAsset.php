<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class BattleImageLazyLoadAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    /**
     * @var list<string>
     */
    public $js = [
        'battle-image-lazy-load.js',
    ];

    /**
     * @var list<string>
     */
    public $css = [
        'battle-image-lazy-load.css',
    ];
}
