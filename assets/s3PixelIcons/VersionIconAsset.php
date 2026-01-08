<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class VersionIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Game Selection Icons';

    /**
     * @var array<string, string>
     */
    public array $fileNameMap = [
        'Splatoon 1.png' => 's1.png',
        'Splatoon 2.png' => 's2.png',
        'Splatoon 3.png' => 's3.png',
    ];
}
