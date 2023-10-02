<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons\internal;

use app\assets\s3PixelIcons\AssetBundle;

abstract class UiIconAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@s3-pixel-icons/UI Icons';

    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [
        '100x Win (Embellished).png' => 'crown_embellished_100x.png',
        '100x Win (Simple).png' => 'crown_100x.png',
        '333x Battle (Embellished).png' => 'crown_embellished_333x.png',
        '333x Battle (Simple).png' => 'crown_333x.png',
        'Anarchy Series - Lose Potential.png' => 'challenge_progress_lose_potential.png',
        'Anarchy Series - Lose.png' => 'challenge_progress_lose.png',
        'Anarchy Series - Win Potential.png' => 'challenge_progress_win_potential.png',
        'Anarchy Series - Win.png' => 'challenge_progress_win.png',
        'Gold Medal.png' => 'gold_medal.png',
        'Golden Eggs.png' => 'golden_egg.png',
        'Inkling.png' => 'inkling.png',
        'Octoling.png' => 'octoling.png',
        'Power Eggs.png' => 'power_egg.png',
        'SR Rescued.png' => 'rescued.png',
        'SR Rescues.png' => 'rescues.png',
        'Silver Medal.png' => 'silver_medal.png',
        'Splats.png' => 'kill.png',
        'Splatted.png' => 'death.png',
        'Top 500 X-Battle (Embellished).png' => 'crown_embellished_x.png',
        'Top 500 X-Battle (Simple).png' => 'crown_x.png',
        'Ultra Signal Attempt.png' => 'signal.png',
    ];
}
