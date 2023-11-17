<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons\internal;

use app\assets\s3PixelIcons\AssetBundle;

abstract class SubSpIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Subs & Specials';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Special - Big Bubbler.png' => 'greatbarrier.png',
        'Special - Booyah Bomb.png' => 'nicedama.png',
        'Special - Crab Tank.png' => 'kanitank.png',
        'Special - Ink Storm.png' => 'amefurashi.png',
        'Special - Ink Vac.png' => 'kyuinki.png',
        'Special - Inkjet.png' => 'jetpack.png',
        'Special - Killer Way 5-0.png' => 'megaphone51.png',
        'Special - Kraken.png' => 'teioika.png',
        'Special - Reefslider.png' => 'sameride.png',
        'Special - Super Chump.png' => 'decoy.png',
        'Special - Tacticooler.png' => 'energystand.png',
        'Special - Tenta Missles.png' => 'missile.png',
        'Special - Triple Inkstrike.png' => 'tripletornado.png',
        'Special - Trizooka.png' => 'ultrashot.png',
        'Special - Ultra Stamp.png' => 'ultrahanko.png',
        'Special - Wave Breaker.png' => 'hopsonar.png',
        'Special - Zipcaster.png' => 'shokuwander.png',
        'Sub - Autobomb.png' => 'robotbomb.png',
        'Sub - Beakon.png' => 'jumpbeacon.png',
        'Sub - Burst Bomb.png' => 'quickbomb.png',
        'Sub - Curling Bomb.png' => 'curlingbomb.png',
        'Sub - Fizzy Bomb.png' => 'tansanbomb.png',
        'Sub - Ink Mine.png' => 'trap.png',
        'Sub - Line Marker.png' => 'linemarker.png',
        'Sub - Point Sensor.png' => 'pointsensor.png',
        'Sub - Splash Wall.png' => 'splashshield.png',
        'Sub - Splat Bomb.png' => 'splashbomb.png',
        'Sub - Sprinkler.png' => 'sprinkler.png',
        'Sub - Suction Bomb.png' => 'kyubanbomb.png',
        'Sub - Torpedo.png' => 'torpedo.png',
        'Sub - Toxic Mist.png' => 'poisonmist.png',
    ];

    /**
     * @var string[]
     */
    public array $dummyFiles = [
        'suminagasheet.png',
    ];
}
