<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\models\StatXPowerDistribAbstract3;

use function floor;
use function max;
use function min;

final class XPowerNormalDistribution
{
    /**
     * @return array{x: int, y: float}[]
     */
    public static function getDistribution(
        int $sampleNumber,
        float $average,
        float $stddev,
        float $minXP,
        float $maxXP,
        int $valueStep = 50,
        int $calcStep = 10,
    ): array {
        $minXP = (int)floor($minXP / $valueStep) * $valueStep;
        $maxXP = (int)floor($maxXP / $valueStep) * $valueStep;

        $nd = new NormalDistribution($average, $stddev);

        $results = [];
        for ($xp = $minXP; $xp <= $maxXP; $xp += $calcStep) {
            $results[] = [
                'x' => $xp,
                // PDF: probability density function; 確率密度関数
                'y' => $sampleNumber * $valueStep * $nd->pdf($xp),
            ];
        }

        return $results;
    }

    /**
     * @param (int|float)[] $xpList
     * @return (array{x: int, y: float}[])|null
     */
    public static function getDistributionFromStatXPowerDistribAbstract3(
        ?StatXPowerDistribAbstract3 $abstract,
        array $xpList,
        int $valueStep = 50,
        int $calcStep = 10,
    ): ?array {
        if (
            !$abstract ||
            !$xpList ||
            $abstract->users < 10 ||
            $abstract->stddev === null ||
            $abstract->median === null
        ) {
            return null;
        }

        return self::getDistribution(
            sampleNumber: (int)$abstract->users,
            average: (float)$abstract->average,
            stddev: (float)$abstract->stddev,
            minXP: (float)$abstract->average - 3 * (float)$abstract->stddev,
            maxXP: (float)$abstract->average + 3 * (float)$abstract->stddev,
            valueStep: $valueStep,
            calcStep: $calcStep,
        );
    }
}
