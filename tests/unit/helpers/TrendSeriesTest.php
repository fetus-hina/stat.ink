<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\TrendSeries;

final class TrendSeriesTest extends Unit
{
    public function testCumulativeRate(): void
    {
        $this->assertSame(
            [100.0, 50.0, 100.0 * 2 / 3, 50.0],
            TrendSeries::cumulativeRate([true, false, true, false]),
        );
        $this->assertSame([], TrendSeries::cumulativeRate([]));
    }

    public function testMovingRate(): void
    {
        // Before the window is filled, the rate of the battles so far is used
        $this->assertSame(
            [0.0, 50.0, 100.0, 50.0, 0.0],
            TrendSeries::movingRate([false, true, true, false, false], 2),
        );
    }

    public function testCumulativeAverageCarriesOverNulls(): void
    {
        $this->assertSame(
            [null, 4.0, 4.0, 3.0, 3.0],
            TrendSeries::cumulativeAverage([null, 4, null, 2, null]),
        );
    }

    public function testCumulativeRatio(): void
    {
        $this->assertSame(
            [
                null, // 0 / 0
                null, // the pair has a null
                3.0, // 3 / 1
                2.0, // (3 + 1) / (1 + 1)
            ],
            TrendSeries::cumulativeRatio([0, 5, 3, 1], [0, null, 1, 1]),
        );
    }

    public function testCarryForward(): void
    {
        $this->assertSame(
            [null, 3, 3, 5, 5, 0],
            TrendSeries::carryForward([null, 3, null, 5, null, 0]),
        );
        $this->assertSame([], TrendSeries::carryForward([]));
    }

    /**
     * @dataProvider providePickIndices
     */
    public function testPickIndices(array $expected, int $count, int $max): void
    {
        $this->assertSame($expected, TrendSeries::pickIndices($count, $max));
    }

    public function providePickIndices(): array
    {
        return [
            'empty' => [[], 0, 3],
            'fewer than max' => [[0, 1, 2], 3, 5],
            'same as max' => [[0, 1, 2], 3, 3],
            'thinned, keeps the first and the last' => [[0, 3, 6, 9], 10, 4],
            'thinned to two' => [[0, 9], 10, 2],
        ];
    }
}
