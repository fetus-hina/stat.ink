<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\DateTimeLocal;

final class DateTimeLocalTest extends Unit
{
    /**
     * @dataProvider provideToFormValue
     */
    public function testToFormValue(mixed $value, mixed $expected): void
    {
        $this->assertSame($expected, DateTimeLocal::toFormValue($value));
    }

    public function provideToFormValue(): array
    {
        return [
            'with seconds' => ['2026-11-27T12:34:56', '2026-11-27 12:34:56'],
            'without seconds' => ['2026-11-27T12:34', '2026-11-27 12:34:00'],
            'already form value' => ['2026-11-27 12:34:56', '2026-11-27 12:34:56'],
            'unixtime notation is kept' => ['@1700000000', '@1700000000'],
            'invalid is kept for the validator' => ['2026-11-27T12', '2026-11-27T12'],
            'empty' => ['', ''],
            'null' => [null, null],
        ];
    }

    /**
     * @dataProvider provideToInputValue
     */
    public function testToInputValue(mixed $value, string $expected): void
    {
        $this->assertSame($expected, DateTimeLocal::toInputValue($value));
    }

    public function provideToInputValue(): array
    {
        return [
            'form value' => ['2026-11-27 12:34:56', '2026-11-27T12:34:56'],
            'not zero-padded' => ['2026-1-2 3:4:5', '2026-01-02T03:04:05'],
            'input value' => ['2026-11-27T12:34', '2026-11-27T12:34:00'],
            'unixtime notation' => ['@1700000000', ''],
            'invalid' => ['yesterday', ''],
            'null' => [null, ''],
        ];
    }
}
