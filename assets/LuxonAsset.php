<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class LuxonAsset extends AssetBundle
{
    public $sourcePath = '@npm/luxon/build/global';
    public $js = [
        'luxon.min.js',
    ];
}
