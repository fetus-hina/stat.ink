<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class SalmonModeIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Salmon Run Icons';

    /**
     * @var array<string, string>
     */
    public array $fileNameMap = [
        'Big Run.png' => 'bigrun.png',
        'Eggstra Work Icon.png' => 'eggstra.png',
        'Salmon Run Menu.png' => 'salmon.png',
    ];
}
