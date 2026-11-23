<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

/**
 * Helpers for stepped line charts (Chart.js `stepped` option)
 */
final class StepChartHelper
{
    /**
     * Returns the point radii that show a point only where the value has changed
     *
     * The first value is not treated as a change. A null value (a gap) has no point,
     * and the next value is compared with the last non-null value.
     *
     * @param array<float|null> $values
     * @return list<int>
     */
    public static function pointRadiiOnChange(array $values, int $radius): array
    {
        $results = [];
        $last = null;
        foreach ($values as $value) {
            if ($value === null) {
                $results[] = 0;
                continue;
            }

            $results[] = $last !== null && $value !== $last ? $radius : 0;
            $last = $value;
        }

        return $results;
    }
}
