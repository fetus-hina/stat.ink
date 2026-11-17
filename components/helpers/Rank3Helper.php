<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use function array_search;
use function is_int;
use function max;

/**
 * Converts Splatoon 3 ranks to numbers for charts
 *
 * C- = 0, C = 1, ..., S = 9, S+0 = 10, S+1 = 11, ...
 */
final class Rank3Helper
{
    private const array RANKS = ['c-', 'c', 'c+', 'b-', 'b', 'b+', 'a-', 'a', 'a+', 's', 's+'];
    private const array LABELS = ['C-', 'C', 'C+', 'B-', 'B', 'B+', 'A-', 'A', 'A+', 'S'];
    private const int S_PLUS = 10;

    /**
     * @param string|null $key key of rank3
     * @param int|null $sPlus the number of S+ (ignored for the other ranks)
     */
    public static function toNumber(?string $key, ?int $sPlus): ?int
    {
        if ($key === null) {
            return null;
        }

        $index = array_search($key, self::RANKS, true);
        if (!is_int($index)) {
            return null;
        }

        return $index === self::S_PLUS
            ? self::S_PLUS + max(0, (int)$sPlus)
            : $index;
    }

    public static function toLabel(int $number): ?string
    {
        return match (true) {
            $number < 0 => null,
            $number < self::S_PLUS => self::LABELS[$number],
            default => 'S+' . ($number - self::S_PLUS),
        };
    }
}
