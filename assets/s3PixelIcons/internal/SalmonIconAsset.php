<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons\internal;

use app\assets\s3PixelIcons\AssetBundle;

abstract class SalmonIconAsset extends AssetBundle
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
        'Bronze Scale.png' => 'scale_bronze.png',
        'Coho Meter 0.png' => 'salmometer-0.png',
        'Coho Meter 1.png' => 'salmometer-1.png',
        'Coho Meter 2.png' => 'salmometer-2.png',
        'Coho Meter 3.png' => 'salmometer-3.png',
        'Coho Meter 4.png' => 'salmometer-4.png',
        'Coho Meter 5.png' => 'salmometer-5.png',
        'Eggstra Work Icon.png' => 'eggstra.png',
        'Gold Scale.png' => 'scale_gold.png',
        'Gone Fission Hydroplant.png' => 'meuniere.png',
        'Hazard MAX.png' => 'hazard-level-max.png',
        'Jammin\' Salmon Junction.png' => 'sujiko.png',
        'Marooner\'s Bay.png' => 'donburako.png',
        'Salmon Run Menu.png' => 'salmon.png',
        'Salmonid Smokeyard.png' => 'tokishirazu.png',
        'Silver Scale.png' => 'scale_silver.png',
        'Sockeye Station.png' => 'aramaki.png',
        'Spawning Grounds.png' => 'dam.png',
        'Tide - High.png' => 'tide-high.png',
        'Tide - Low.png' => 'tide-low.png',
        'Tide - Medium.png' => 'tide-mid.png',
    ];
}
