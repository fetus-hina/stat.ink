<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use InvalidArgumentException;

use function abs;
use function floor;
use function max;
use function pow;
use function round;
use function sqrt;

/**
 * Helpers for histograms of discrete values (values on a fixed grid, e.g. 0.025 steps).
 *
 * Values are handled as integer multiples of the grid unit internally to avoid
 * floating point errors (e.g. 0.725 / 0.025 = 28.999...).
 */
final class DiscreteHistogram
{
    private const PRECISION = 6;

    /**
     * @param iterable<array{float, int}> $rows list of [value, count]
     * @return array{n: int, mean: float, stddev: float|null}|null
     */
    public static function summarize(iterable $rows): ?array
    {
        $n = 0;
        $sum = 0.0;
        foreach ($rows as [$value, $count]) {
            $n += $count;
            $sum += $value * $count;
        }

        if ($n < 1) {
            return null;
        }

        $mean = $sum / $n;
        if ($n < 2) {
            return ['n' => $n, 'mean' => $mean, 'stddev' => null];
        }

        $sqDev = 0.0;
        foreach ($rows as [$value, $count]) {
            $sqDev += ($value - $mean) ** 2 * $count;
        }

        return [
            'n' => $n,
            'mean' => $mean,
            'stddev' => sqrt($sqDev / ($n - 1)),
        ];
    }

    /**
     * Bin width by Scott's rule (3.5σ / n^(1/3)), rounded to a multiple of the grid unit.
     *
     * Returns the unit itself when the width cannot be determined.
     */
    public static function scottWidth(int $n, ?float $stddev, float $unit): float
    {
        if ($n < 2 || $stddev === null || $stddev <= 0.0) {
            return $unit;
        }

        $width = 3.5 * $stddev / pow($n, 1.0 / 3.0);
        $units = max(1, (int)round($width / $unit));

        return round($units * $unit, self::PRECISION);
    }

    /**
     * Lower bound of the bin that the value belongs to.
     *
     * @param float|null $overflow values greater than or equal to this are merged into the bin starting at this value
     */
    public static function binStart(float $value, float $width, float $unit, ?float $overflow = null): float
    {
        $widthUnits = self::toUnits($width, $unit);
        if ($widthUnits < 1 || abs($widthUnits * $unit - $width) > 1e-9) {
            throw new InvalidArgumentException('The width must be a positive multiple of the unit');
        }

        $valueUnits = self::toUnits($value, $unit);
        if ($overflow !== null && $valueUnits >= self::toUnits($overflow, $unit)) {
            return round($overflow, self::PRECISION);
        }

        $startUnits = (int)floor($valueUnits / $widthUnits) * $widthUnits;

        return round($startUnits * $unit, self::PRECISION);
    }

    private static function toUnits(float $value, float $unit): int
    {
        return (int)round($value / $unit);
    }
}
