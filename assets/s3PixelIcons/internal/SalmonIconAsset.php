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
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Big Run.png' => 'bigrun.png',
        'Bronze Scale.png' => 'scale_bronze.png',
        'Coho Meter 0.png' => ['salmometer-0.png', 'salmometer-yokozuna-0.png'],
        'Coho Meter 1.png' => ['salmometer-1.png', 'salmometer-yokozuna-1.png'],
        'Coho Meter 2.png' => ['salmometer-2.png', 'salmometer-yokozuna-2.png'],
        'Coho Meter 3.png' => ['salmometer-3.png', 'salmometer-yokozuna-3.png'],
        'Coho Meter 4.png' => ['salmometer-4.png', 'salmometer-yokozuna-4.png'],
        'Coho Meter 5.png' => ['salmometer-5.png', 'salmometer-yokozuna-5.png'],
        'Eggstra Work Icon.png' => 'eggstra.png',
        'Gold Scale.png' => 'scale_gold.png',
        'Gone Fission Hydroplant.png' => 'meuniere.png',
        'Hazard MAX.png' => 'hazard-level-max.png',
        'Horo Meter 0.png' => 'salmometer-tatsu-0.png',
        'Horo Meter 1.png' => 'salmometer-tatsu-1.png',
        'Horo Meter 2.png' => 'salmometer-tatsu-2.png',
        'Horo Meter 3.png' => 'salmometer-tatsu-3.png',
        'Horo Meter 4.png' => 'salmometer-tatsu-4.png',
        'Horo Meter 5.png' => 'salmometer-tatsu-5.png',
        'Jammin\' Salmon Junction.png' => 'sujiko.png',
        'Marooner\'s Bay.png' => 'donburako.png',
        'Megalo Meter 0.png' => 'salmometer-jaw-0.png',
        'Megalo Meter 1.png' => 'salmometer-jaw-1.png',
        'Megalo Meter 2.png' => 'salmometer-jaw-2.png',
        'Megalo Meter 3.png' => 'salmometer-jaw-3.png',
        'Megalo Meter 4.png' => 'salmometer-jaw-4.png',
        'Megalo Meter 5.png' => 'salmometer-jaw-5.png',
        'Salmon Run Menu.png' => 'salmon.png',
        'Salmonid Smokeyard.png' => 'tokishirazu.png',
        'Silver Scale.png' => 'scale_silver.png',
        'Sockeye Station.png' => 'aramaki.png',
        'Spawning Grounds.png' => 'dam.png',
        'Tide - High.png' => 'tide-high.png',
        'Tide - Low.png' => 'tide-low.png',
        'Tide - Mid.png' => 'tide-mid.png',
    ];

    public array $dummyFiles = [
        'donpiko.png',
    ];
}
