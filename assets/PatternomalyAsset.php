<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

class PatternomalyAsset extends AssetBundle
{
    public $sourcePath = '@npm/patternomaly/dist';
    public $js = [
        'patternomaly.js',
    ];
}
