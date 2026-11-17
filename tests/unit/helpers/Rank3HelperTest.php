<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\Rank3Helper;

final class Rank3HelperTest extends Unit
{
    /**
     * @dataProvider provideToNumber
     */
    public function testToNumber(?int $expected, ?string $key, ?int $sPlus): void
    {
        $this->assertSame($expected, Rank3Helper::toNumber($key, $sPlus));
    }

    public function provideToNumber(): array
    {
        return [
            'C-' => [0, 'c-', null],
            'A+' => [8, 'a+', null],
            'S' => [9, 's', null],
            'S+ without the number' => [10, 's+', null],
            'S+0' => [10, 's+', 0],
            'S+50' => [60, 's+', 50],
            'the number is ignored below S+' => [9, 's', 5],
            'unknown key' => [null, 'x', null],
            'null' => [null, null, 3],
        ];
    }

    /**
     * @dataProvider provideToLabel
     */
    public function testToLabel(?string $expected, int $number): void
    {
        $this->assertSame($expected, Rank3Helper::toLabel($number));
    }

    public function provideToLabel(): array
    {
        return [
            'C-' => ['C-', 0],
            'S' => ['S', 9],
            'S+0' => ['S+0', 10],
            'S+50' => ['S+50', 60],
            'out of range' => [null, -1],
        ];
    }
}
