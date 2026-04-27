<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\XPowerNormalDistribution;

use function array_column;
use function count;
use function max;

class XPowerNormalDistributionTest extends Unit
{
    public function testDistributionSpansClampedRange(): void
    {
        // minXP = 1737 -> floor to 1700 (valueStep=50); maxXP = 2363 -> ceil to 2400.
        $points = XPowerNormalDistribution::getDistribution(
            sampleNumber: 100,
            average: 2050.0,
            stddev: 100.0,
            minXP: 1737.0,
            maxXP: 2363.0,
            valueStep: 50,
            calcStep: 10,
        );
        $this->assertNotEmpty($points);

        $xs = array_column($points, 'x');
        $this->assertSame(1700, $xs[0]);
        $this->assertSame(2400, $xs[count($xs) - 1]);

        // Step should be calcStep.
        $this->assertSame(10, $xs[1] - $xs[0]);
    }

    public function testDistributionPeaksNearAverage(): void
    {
        $points = XPowerNormalDistribution::getDistribution(
            sampleNumber: 1000,
            average: 2000.0,
            stddev: 100.0,
            minXP: 1700.0,
            maxXP: 2300.0,
            valueStep: 50,
            calcStep: 10,
        );

        $maxY = max(array_column($points, 'y'));
        $peakX = null;
        foreach ($points as $point) {
            if ($point['y'] === $maxY) {
                $peakX = $point['x'];
                break;
            }
        }

        // The peak of a Normal(mean=2000) PDF on a symmetric grid should sit at 2000.
        $this->assertSame(2000, $peakX);
        $this->assertGreaterThan(0.0, $maxY);
    }

    public function testEachPointHasExpectedShape(): void
    {
        $points = XPowerNormalDistribution::getDistribution(
            sampleNumber: 50,
            average: 2000.0,
            stddev: 50.0,
            minXP: 1900.0,
            maxXP: 2100.0,
        );
        foreach ($points as $point) {
            $this->assertArrayHasKey('x', $point);
            $this->assertArrayHasKey('y', $point);
            $this->assertIsInt($point['x']);
            $this->assertIsFloat($point['y']);
            $this->assertGreaterThanOrEqual(0.0, $point['y']);
        }
    }
}
