<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class SalmonBossIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Salmon Run Bosses';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Big Shot.png' => 'tekkyu.png',
        'Cohozuna.png' => 'yokozuna.png',
        'Drizzler.png' => 'koumori.png',
        'Fish Stick.png' => 'hashira.png',
        'Flipper Flopper.png' => 'diver.png',
        'Flyfish.png' => 'katapad.png',
        'Goldie.png' => 'kin_shake.png',
        'Griller.png' => 'grill.png',
        'Gusher.png' => 'doro_shake.png',
        'Horrorboros.png' => 'tatsu.png',
        'Maws.png' => 'mogura.png',
        'Scrapper.png' => 'teppan.png',
        'Slammin\' Lid.png' => 'nabebuta.png',
        'Steel Eel.png' => 'hebi.png',
        'Steelhead.png' => 'bakudan.png',
        'Stinger.png' => 'tower.png',
    ];
}
