<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class DsegAsset extends AssetBundle
{
    public $css = [
        'https://fonts.stats.ink/dseg/dseg7-classic.min.css',
    ];
}
