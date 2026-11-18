<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use InvalidArgumentException;
use app\components\helpers\DiscreteHistogram;

class DiscreteHistogramTest extends Unit
{
    public function testSummarizeEmptyReturnsNull(): void
    {
        $this->assertNull(DiscreteHistogram::summarize([]));
        $this->assertNull(DiscreteHistogram::summarize([[1.0, 0]]));
    }

    public function testSummarizeSingleSample(): void
    {
        $result = DiscreteHistogram::summarize([[12.5, 1]]);
        $this->assertNotNull($result);
        $this->assertSame(1, $result['n']);
        $this->assertEqualsWithDelta(12.5, $result['mean'], 1e-12);
        $this->assertNull($result['stddev']);
    }

    public function testSummarizeWeighted(): void
    {
        // samples: 1, 1, 3, 3, 3, 5 => mean = 16/6, sample variance = (sum sq dev) / 5
        $result = DiscreteHistogram::summarize([[1.0, 2], [3.0, 3], [5.0, 1]]);
        $this->assertNotNull($result);
        $this->assertSame(6, $result['n']);

        $mean = 16 / 6;
        $var = (2 * (1 - $mean) ** 2 + 3 * (3 - $mean) ** 2 + (5 - $mean) ** 2) / 5;
        $this->assertEqualsWithDelta($mean, $result['mean'], 1e-12);
        $this->assertEqualsWithDelta(sqrt($var), $result['stddev'], 1e-12);
    }

    public function testScottWidthRoundsToUnit(): void
    {
        // 3.5 * 2.274 / 305424^(1/3) = 0.118... => 5 units of 0.025 = 0.125
        $this->assertEqualsWithDelta(0.125, DiscreteHistogram::scottWidth(305424, 2.274, 0.025), 1e-9);

        // 3.5 * 5.57 / 289690^(1/3) = 0.294... => 3 units of 0.1 = 0.3
        $this->assertEqualsWithDelta(0.3, DiscreteHistogram::scottWidth(289690, 5.57, 0.1), 1e-9);
    }

    public function testScottWidthIsAtLeastOneUnit(): void
    {
        $this->assertEqualsWithDelta(0.025, DiscreteHistogram::scottWidth(1000000, 0.01, 0.025), 1e-9);
    }

    public function testScottWidthFallsBackToUnitWhenUndefined(): void
    {
        $this->assertEqualsWithDelta(0.1, DiscreteHistogram::scottWidth(1, null, 0.1), 1e-9);
        $this->assertEqualsWithDelta(0.1, DiscreteHistogram::scottWidth(100, 0.0, 0.1), 1e-9);
    }

    public function testBinStart(): void
    {
        // 0.725 / 0.025 is not exactly 29 in floating point; must still be handled as 29 units
        $this->assertEqualsWithDelta(0.625, DiscreteHistogram::binStart(0.725, 0.125, 0.025), 1e-9);
        $this->assertEqualsWithDelta(0.75, DiscreteHistogram::binStart(0.75, 0.125, 0.025), 1e-9);
        $this->assertEqualsWithDelta(0.0, DiscreteHistogram::binStart(0.0, 0.125, 0.025), 1e-9);
        $this->assertEqualsWithDelta(12.3, DiscreteHistogram::binStart(12.3, 0.3, 0.1), 1e-9);
        $this->assertEqualsWithDelta(12.3, DiscreteHistogram::binStart(12.5, 0.3, 0.1), 1e-9);
    }

    public function testBinStartWithOverflowBin(): void
    {
        $this->assertEqualsWithDelta(1.75, DiscreteHistogram::binStart(1.975, 0.25, 0.025, 2.0), 1e-9);
        $this->assertEqualsWithDelta(2.0, DiscreteHistogram::binStart(2.0, 0.25, 0.025, 2.0), 1e-9);
        $this->assertEqualsWithDelta(2.0, DiscreteHistogram::binStart(7.175, 0.25, 0.025, 2.0), 1e-9);
    }

    public function testBinStartRejectsWidthNotMultipleOfUnit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DiscreteHistogram::binStart(1.0, 0.25, 0.1);
    }
}
