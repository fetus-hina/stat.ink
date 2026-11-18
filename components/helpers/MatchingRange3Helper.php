<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

/**
 * Shared definitions for the Matching Range (MR) stats of Splatoon 3
 */
final class MatchingRange3Helper
{
    /**
     * The first version that uses the MR-based team division for each lobby (lobby key => version tag)
     */
    public const MIN_VERSIONS = [
        'xmatch' => '11.1.0',
        'bankara_challenge' => '12.0.0',
    ];

    /**
     * Battles in X Battle are classified by the uploader's X Power with this threshold
     */
    public const X_POWER_THRESHOLD = 2000;
}
