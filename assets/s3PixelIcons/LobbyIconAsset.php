<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class LobbyIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Game Modes & Lobby Icons';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Anarchy.png' => ['bankara.png', 'bankara_challenge.png', 'bankara_open.png'],
        'Challenge.png' => 'event.png',
        'Private Battle.png' => 'private.png',
        'Splatfest.png' => ['splatfest.png', 'splatfest_challenge.png', 'splatfest_open.png'],
        'Turf War.png' => 'regular.png',
        'X Battles.png' => 'xmatch.png',
    ];
}
