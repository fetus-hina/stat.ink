<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class LineSeedJpAsset extends AssetBundle
{
    public $css = [
        'https://fonts.stats.ink/lineseedjp/lineseedjp-otf-bd.min.css',
        'https://fonts.stats.ink/lineseedjp/lineseedjp-otf-rg.min.css',
    ];
}
