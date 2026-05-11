<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class ApiFetchAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';

    public $js = [
        'api-fetch.js',
    ];
}
