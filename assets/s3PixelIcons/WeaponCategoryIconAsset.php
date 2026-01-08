<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class WeaponCategoryIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Generic Weapon Icons';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Blasters.png' => 'blaster.png',
        'Brellas.png' => 'brella.png',
        'Brushes.png' => 'brush.png',
        'Chargers.png' => 'charger.png',
        'Dualies.png' => 'maneuver.png',
        'Rollers.png' => 'roller.png',
        'Shooters.png' => ['reelgun.png', 'shooter.png'],
        'Sloshers.png' => 'slosher.png',
        'Splatanas.png' => 'wiper.png',
        'Splatlings.png' => 'spinner.png',
        'Stringers.png' => 'stringer.png',
    ];
}
