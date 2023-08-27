<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class UiIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/UI Icons';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Golden Eggs.png' => 'golden_egg.png',
        'Power Eggs.png' => 'power_egg.png',
        'SR Rescued.png' => 'rescued.png',
        'SR Rescues.png' => 'rescues.png',
        'Splats.png' => 'kill.png',
        'Splatted.png' => 'death.png',
    ];
}
