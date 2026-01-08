<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

final class AbilityIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/Chunks Icons/Square Frames';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        'Chunk - 2x Ability.png' => 'ability_doubler.png',
        'Chunk - Bomb Resist.png' => 'sub_resistance_up.png',
        'Chunk - Comeback.png' => 'comeback.png',
        'Chunk - Drop Roller.png' => 'drop_roller.png',
        'Chunk - Haunt.png' => 'haunt.png',
        'Chunk - Ink Recovery Up.png' => 'ink_recovery_up.png',
        'Chunk - Ink Recovery.png' => 'ink_recovery_up.png',
        'Chunk - Ink Resist.png' => 'ink_resistance_up.png',
        'Chunk - Intensify Action.png' => 'intensify_action.png',
        'Chunk - Last Ditch Effort.png' => 'last_ditch_effort.png',
        'Chunk - Main Saver.png' => 'ink_saver_main.png',
        'Chunk - Main saver.png' => 'ink_saver_main.png',
        'Chunk - Ninja Squid.png' => 'ninja_squid.png',
        'Chunk - Object Shredder.png' => 'object_shredder.png',
        'Chunk - Opening Gambit.png' => 'opening_gambit.png',
        'Chunk - Quick Respawn.png' => 'quick_respawn.png',
        'Chunk - Quick Super Jump.png' => 'quick_super_jump.png',
        'Chunk - Respawn Punisher.png' => 'respawn_punisher.png',
        'Chunk - Run Speed Up.png' => 'run_speed_up.png',
        'Chunk - Special Charge Up.png' => 'special_charge_up.png',
        'Chunk - Special Power Up.png' => 'special_power_up.png',
        'Chunk - Special Saver.png' => 'special_saver.png',
        'Chunk - Stealth Jump.png' => 'stealth_jump.png',
        'Chunk - Sub Power Up.png' => 'sub_power_up.png',
        'Chunk - Sub Saver.png' => 'ink_saver_sub.png',
        'Chunk - Swim Speed Up.png' => 'swim_speed_up.png',
        'Chunk - Tenacity.png' => 'tenacity.png',
        'Chunk - Thermal Ink.png' => 'thermal_ink.png',
        'Chunk - Unassigned.png' => 'unknown.png',
    ];
}
