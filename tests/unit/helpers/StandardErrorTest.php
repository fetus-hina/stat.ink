<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\StandardError;

class StandardErrorTest extends Unit
{
    public function testReturnsNullWhenSampleTooSmall(): void
    {
        $this->assertNull(StandardError::winpct(0, 9));
        $this->assertNull(StandardError::winpct(5, 9));
    }

    public function testReturnsNullWhenStdErrIsZero(): void
    {
        // 0 wins / 10 battles -> rate2 is 1.0, rate1 is 0.0, the product is 0,
        // so stderr ends up below the threshold and the helper returns null.
        $this->assertNull(StandardError::winpct(0, 10));
        $this->assertNull(StandardError::winpct(10, 10));
    }

    public function testBasicStructureAndRate(): void
    {
        $result = StandardError::winpct(50, 100);
        $this->assertIsArray($result);
        $this->assertEqualsWithDelta(0.5, $result['rate'], 1e-12);
        $this->assertGreaterThan(0.0, $result['stderr']);
        $this->assertEqualsWithDelta($result['stderr'] * 1.96, $result['err95ci'], 1e-12);
        $this->assertEqualsWithDelta($result['stderr'] * 2.58, $result['err99ci'], 1e-12);

        // 95% CI must be tighter than 99% CI.
        $this->assertGreaterThan($result['min99ci'], $result['min95ci']);
        $this->assertLessThan($result['max99ci'], $result['max95ci']);

        // CI bounds must be clamped to [0, 1].
        $this->assertGreaterThanOrEqual(0.0, $result['min95ci']);
        $this->assertLessThanOrEqual(1.0, $result['max95ci']);
        $this->assertGreaterThanOrEqual(0.0, $result['min99ci']);
        $this->assertLessThanOrEqual(1.0, $result['max99ci']);
    }

    public function testCenteredAroundFiftyPercentIsNotSignificant(): void
    {
        $result = StandardError::winpct(50, 100);
        $this->assertSame('', $result['significant']);
    }

    public function testStrongWinRateIsSignificantWithDoubleStar(): void
    {
        // Very high win rate over a huge sample should land deep in the "significant" zone.
        $result = StandardError::winpct(900, 1000);
        $this->assertSame('**', $result['significant']);
    }

    public function testCiClampsAtOneForExtremeWinRate(): void
    {
        // 99/100 produces a 99% upper bound that would exceed 1.0 if not clamped.
        $result = StandardError::winpct(99, 100);
        $this->assertSame(1.0, $result['max99ci']);
        $this->assertSame(1.0, $result['max95ci']);
    }
}
