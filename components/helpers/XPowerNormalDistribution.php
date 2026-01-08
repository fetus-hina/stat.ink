<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use MathPHP\Probability\Distribution\Continuous\Normal as NormalDistribution;
use app\models\StatXPowerDistribAbstract3;

use function ceil;
use function floor;

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
        $maxXP = (int)ceil($maxXP / $valueStep) * $valueStep;

        $nd = new NormalDistribution($average, $stddev);

        $results = [];
        for ($xp = $minXP; $xp <= $maxXP; $xp += $calcStep) {
            $results[] = [
                'x' => $xp,
                // PDF: probability density function; 確率密度関数
                'y' => (float)($sampleNumber * $valueStep * $nd->pdf($xp)),
            ];
        }

        return $results;
    }

    /**
     * @return (array{x: int, y: float}[])|null
     */
    public static function getDistributionFromStatXPowerDistribAbstract3(
        ?StatXPowerDistribAbstract3 $abstract,
        int $calcStep = 10,
    ): ?array {
        if (
            !$abstract ||
            $abstract->users < 10 ||
            $abstract->stddev === null ||
            $abstract->median === null ||
            $abstract->histogram_width === null ||
            $abstract->histogram_width < 2
        ) {
            return null;
        }

        return self::getDistribution(
            sampleNumber: (int)$abstract->users,
            average: (float)$abstract->average,
            stddev: (float)$abstract->stddev,
            minXP: (float)$abstract->average - 3 * (float)$abstract->stddev,
            maxXP: (float)$abstract->average + 3 * (float)$abstract->stddev,
            valueStep: $abstract->histogram_width,
            calcStep: $calcStep,
        );
    }
}
