<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\StepChartHelper;

final class StepChartHelperTest extends Unit
{
    /**
     * @param int[] $expected
     * @param array<float|null> $values
     * @dataProvider providePointRadiiOnChange
     */
    public function testPointRadiiOnChange(array $expected, array $values): void
    {
        $this->assertSame($expected, StepChartHelper::pointRadiiOnChange($values, 3));
    }

    public function providePointRadiiOnChange(): array
    {
        return [
            'empty' => [[], []],
            'the first value is not a change' => [[0], [2000.0]],
            'unchanged values' => [[0, 0, 0], [2000.0, 2000.0, 2000.0]],
            'changed values' => [[0, 0, 3, 0, 3], [2000.0, 2000.0, 2050.5, 2050.5, 2010.0]],
            'nulls have no point' => [[0, 0, 3], [null, 2000.0, 2100.0]],
            'compare with the last non-null value' => [[0, 0, 0, 0, 3], [2000.0, null, 2000.0, null, 2100.0]],
            'all nulls' => [[0, 0], [null, null]],
        ];
    }

    public function testPointRadiiOnChangeRadius(): void
    {
        $this->assertSame([0, 5], StepChartHelper::pointRadiiOnChange([1.0, 2.0], 5));
    }

    public function testPointRadiiOnChangeKeepsKeysAsList(): void
    {
        $this->assertSame([0, 3], StepChartHelper::pointRadiiOnChange([5 => 1.0, 9 => 2.0], 3));
    }
}
