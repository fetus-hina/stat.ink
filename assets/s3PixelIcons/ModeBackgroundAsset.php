<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class ModeBackgroundAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Mode Backgrounds';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Anarchy.png' => ['bankara_challenge.png', 'bankara_open.png'],
        'Challenge.png' => 'event.png',
        'Private Battle.png' => 'private.png',
        'Salmon Run.png' => 'salmon.png',
        'Splatfest Colorful.png' => ['splatfest_challenge.png', 'splatfest_open.png'],
        'Turf War.png' => 'regular.png',
        'X-Battle.png' => 'xmatch.png',
    ];
}
