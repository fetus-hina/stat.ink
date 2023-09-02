<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons\internal;

use app\assets\s3PixelIcons\AssetBundle;

abstract class WeaponIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Weapons';

    /**
     * @var array<string, string>
     */
    public array $fileNameMap = [
        '52 Gal.png' => '52gal.png',
        '96 Gal Deco.png' => '96gal.png',
        '96 Gal.png' => '96gal_deco.png',
        'Aerospray MG.png' => 'promodeler_mg.png',
        'Aerospray RG.png' => 'promodeler_rg.png',
        'Annaki Splattershot Nova.png' => 'spaceshooter_collabo.png',
        'Ballpoint Splatling Nouveau.png' => 'kugelschreiber_hue.png',
        'Ballpoint Splatling.png' => 'kugelschreiber.png',
        'Bamboozler.png' => 'bamboo14mk1.png',
        'Big Swig Roller Express.png' => 'wideroller_collabo.png',
        'Big Swig Roller.png' => 'wideroller.png',
        'Blaster.png' => 'hotblaster.png',
        'Bloblobber Deco.png' => 'furo_deco.png',
        'Bloblobber.png' => 'furo.png',
        'Carbon Roller Deco.png' => 'carbon_deco.png',
        'Carbon Roller.png' => 'carbon.png',
        'Clash Blaster Neo.png' => 'clashblaster_neo.png',
        'Clash Blaster.png' => 'clashblaster.png',
        'Classic Squiffer.png' => 'squiclean_a.png',
        'Custom Goo Tuber.png' => 'soytuber_custom.png',
        'Custom Jet Squelcher.png' => 'jetsweeper_custom.png',
        'Custom Splattershot Jr.png' => 'momiji.png',
        'Custom Squelcher Dualies.png' => 'dualsweeper_custom.png',
        'Dapple Dualies Nouveau.png' => 'sputtery_hue.png',
        'Dapple Dualies.png' => 'sputtery.png',
        'Dark Tetra Dualies.png' => 'quadhopper_black.png',
        'Dread Wringer.png' => 'moprin.png',
        'Dynamo Roller.png' => 'dynamo.png',
        'E-Liter 4k Scope.png' => 'liter4k_scope.png',
        'E-Liter 4k.png' => 'liter4k.png',
        'Explosher.png' => 'explosher.png',
        'Flingza Roller.png' => 'variableroller.png',
        'Forge Splattershot Pro.png' => 'prime_collabo.png',
        'Glooga Dualies.png' => 'kelvin525.png',
        'Gold Dynamo Roller.png' => 'dynamo_tesla.png',
        'Golden Rotation.png' => 'random_rare.png',
        'Goo Tuber.png' => 'soytuber.png',
        'Grizzco Blaster.png' => 'kuma_blaster.png',
        'Grizzco Brella.png' => 'kuma_shelter.png',
        'Grizzco Charger.png' => 'kuma_charger.png',
        'Grizzco Dualies.png' => 'kuma_maneuver.png',
        'Grizzco Slosher.png' => 'kuma_slosher.png',
        'Grizzco Splatana.png' => 'kuma_wiper.png',
        'H-3 Nozzlenose D.png' => 'h3reelgun_d.png',
        'H-3 Nozzlenose.png' => 'h3reelgun.png',
        'Heavy Edit Splatling.png' => 'examiner.png',
        'Heavy Splatling Deco.png' => 'barrelspinner_deco.png',
        'Heavy Splatling.png' => 'barrelspinner.png',
        'Hero Shot Replica.png' => 'heroshooter_replica.png',
        'Hydra Splatling.png' => 'hydra.png',
        'Inkbrush Nouveau.png' => 'pablo_hue.png',
        'Inkbrush.png' => 'pablo.png',
        'Inkline Tri-Stringer.png' => 'tristringer_collabo.png',
        'Jet Squelcher.png' => 'jetsweeper.png',
        'Krak-On Splat Roller.png' => 'splatroller_collabo.png',
        'L-3 Nozzlenose D.png' => 'l3reelgun_d.png',
        'L-3 Nozzlenose.png' => 'l3reelgun.png',
        'Light Tetra Dualies.png' => 'quadhopper_white.png',
        'Luna Blaster Neo.png' => 'nova_neo.png',
        'Luna Blaster.png' => 'nova.png',
        'Mini Splatling.png' => 'splatspinner.png',
        'N-ZAP 85.png' => 'nzap85.png',
        'N-ZAP 89.png' => 'nzap89.png',
        'Nautilus 47.png' => 'nautilus47.png',
        'Neo Splash-o-matic.png' => 'sharp_neo.png',
        'Neo Sploosh-o-matic.png' => 'bold_neo.png',
        'Octobrush Nouveau.png' => 'hokusai_hue.png',
        'Octobrush.png' => 'hokusai.png',
        'Painbrush.png' => 'fincent.png',
        'REEF-LUX 450.png' => 'lact450.png',
        'Random Rotation.png' => 'random.png',
        'Range Blaster.png' => 'longblaster.png',
        'Rapid Blaster Deco.png' => 'rapid_deco.png',
        'Rapid Blaster Pro Deco.png' => 'rapid_elite_deco.png',
        'Rapid Blaster Pro.png' => 'rapid_elite.png',
        'Rapid Blaster.png' => 'rapid.png',
        'S-BLAST \'92.png' => 'sblast92.png',
        'Slosher Deco.png' => 'bucketslosher_deco.png',
        'Slosher.png' => 'bucketslosher.png',
        'Sloshing Machine Neo.png' => 'screwslosher_neo.png',
        'Sloshing Machine.png' => 'screwslosher.png',
        'Snipewriter 5H.png' => 'rpen_5h.png',
        'Sorella Brella.png' => 'parashelter_sorella.png',
        'Splash-o-matic.png' => 'sharp.png',
        'Splat Brella.png' => 'parashelter.png',
        'Splat Charger.png' => 'splatcharger.png',
        'Splat Dualies.png' => 'maneuver.png',
        'Splat Roller.png' => 'splatroller.png',
        'Splatana Stamper.png' => 'jimuwiper.png',
        'Splatana Wiper Deco.png' => 'drivewiper_deco.png',
        'Splatana Wiper.png' => 'drivewiper.png',
        'Splatterscope.png' => 'splatscope.png',
        'Splattershot Jr.png' => 'wakaba.png',
        'Splattershot Nova.png' => 'spaceshooter.png',
        'Splattershot Pro.png' => 'prime.png',
        'Splattershot.png' => 'sshooter.png',
        'Sploosh-o-matic.png' => 'bold.png',
        'Squeezer.png' => 'bottlegeyser.png',
        'Squelcher Dualies.png' => 'dualsweeper.png',
        'Tenta Brella.png' => 'campingshelter.png',
        'Tenta Sorella Brella.png' => 'campingshelter_sorella.png',
        'Tentatek Splattershot.png' => 'sshooter_collabo.png',
        'Tri-Slosher Nouveau.png' => 'hissen_hue.png',
        'Tri-Slosher.png' => 'hissen.png',
        'Tri-Stringer.png' => 'tristringer.png',
        'Undercover Brella.png' => 'spygadget.png',
        'Z+F Splat Charger.png' => 'splatcharger_collabo.png',
        'Z+F Splatterscope.png' => 'splatscope_collabo.png',
        'Zink Mini Splatling.png' => 'splatspinner_collabo.png',
    ];
}
