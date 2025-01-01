<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons\internal;

use app\assets\s3PixelIcons\AssetBundle;

abstract class LobbyRuleIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Game Modes & Lobby Icons';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Anarchy.png' => [
            'bankara.png',
            'bankara_challenge.png',
            'bankara_open.png',
        ],
        'Challenge.png' => 'event.png',
        'Clam Blitz.png' => 'asari.png',
        'Private Battle.png' => 'private.png',
        'Rainmaker.png' => 'hoko.png',
        'Splat Zones.png' => 'area.png',
        'Splatfest.png' => [
            'splatfest.png',
            'splatfest_challenge.png',
            'splatfest_open.png',
        ],
        'Tower Control.png' => 'yagura.png',
        'Tricolor Attacker.png' => 'tricolor-attacker.png',
        'Tricolor Defender.png' => 'tricolor-defender.png',
        'Tricolor Turf War.png' => 'tricolor.png',
        'Turf War.png' => [
            'nawabari.png',
            'regular.png',
        ],
        'X Battles.png' => 'xmatch.png',
    ];
}
