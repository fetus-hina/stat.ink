<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use function array_map;
use function array_shift;
use function count;
use function min;
use function round;

/**
 * Builds time series (one value per battle) for trend charts
 */
final class TrendSeries
{
    /**
     * Winning percentage of all the battles so far
     *
     * @param list<bool> $wins
     * @return list<float>
     */
    public static function cumulativeRate(array $wins): array
    {
        $ret = [];
        $battles = 0;
        $winCount = 0;
        foreach ($wins as $win) {
            ++$battles;
            $winCount += $win ? 1 : 0;
            $ret[] = 100.0 * $winCount / $battles;
        }

        return $ret;
    }

    /**
     * Winning percentage of the last $window battles
     *
     * Before $window battles are played, the rate of the battles so far is used.
     *
     * @param list<bool> $wins
     * @param positive-int $window
     * @return list<float>
     */
    public static function movingRate(array $wins, int $window): array
    {
        $ret = [];
        $queue = [];
        $winCount = 0;
        foreach ($wins as $win) {
            $queue[] = $win;
            $winCount += $win ? 1 : 0;
            if (count($queue) > $window) {
                $winCount -= array_shift($queue) ? 1 : 0;
            }

            $ret[] = 100.0 * $winCount / count($queue);
        }

        return $ret;
    }

    /**
     * Average of the non-null values so far (null until the first non-null value)
     *
     * @param list<int|float|null> $values
     * @return list<float|null>
     */
    public static function cumulativeAverage(array $values): array
    {
        $ret = [];
        $total = 0;
        $count = 0;
        foreach ($values as $value) {
            if ($value !== null) {
                $total += $value;
                ++$count;
            }

            $ret[] = $count > 0 ? (float)($total / $count) : null;
        }

        return $ret;
    }

    /**
     * sum($numerators) / sum($denominators) of the pairs so far, skipping pairs with null
     *
     * Null while the sum of the denominators is zero.
     *
     * @param list<int|null> $numerators
     * @param list<int|null> $denominators same length as $numerators
     * @return list<float|null>
     */
    public static function cumulativeRatio(array $numerators, array $denominators): array
    {
        $ret = [];
        $num = 0;
        $den = 0;
        foreach ($numerators as $i => $n) {
            $d = $denominators[$i] ?? null;
            if ($n !== null && $d !== null) {
                $num += $n;
                $den += $d;
            }

            $ret[] = $den > 0 ? (float)($num / $den) : null;
        }

        return $ret;
    }

    /**
     * Replaces each null with the last non-null value before it (null until the first non-null value)
     *
     * @template T of int|float
     * @param list<T|null> $values
     * @return list<T|null>
     */
    public static function carryForward(array $values): array
    {
        $ret = [];
        $last = null;
        foreach ($values as $value) {
            if ($value !== null) {
                $last = $value;
            }

            $ret[] = $last;
        }

        return $ret;
    }

    /**
     * Evenly spaced indices to thin out $count points to at most $max points
     *
     * The first and the last points are always included.
     *
     * @param int<2, max> $max
     * @return list<int>
     */
    public static function pickIndices(int $count, int $max): array
    {
        if ($count <= $max) {
            $ret = [];
            for ($i = 0; $i < $count; ++$i) {
                $ret[] = $i;
            }

            return $ret;
        }

        $ret = [];
        for ($i = 0; $i < $max; ++$i) {
            $ret[] = min($count - 1, (int)round($i * ($count - 1) / ($max - 1)));
        }

        return $ret;
    }

    /**
     * @template T
     * @param list<T> $values
     * @param list<int> $indices
     * @return list<T>
     */
    public static function pick(array $values, array $indices): array
    {
        return array_map(fn (int $i): mixed => $values[$i], $indices);
    }
}
