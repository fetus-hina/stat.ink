<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use DateTimeImmutable;
use DateTimeZone;
use app\components\helpers\YearMonth;

final class YearMonthTest extends Unit
{
    /**
     * @dataProvider provideDateStrings
     */
    public function testFromDateString(string $input, int $expected): void
    {
        $this->assertSame($expected, YearMonth::fromDateString($input));
    }

    public function provideDateStrings(): array
    {
        return [
            'UTC start of month' => ['2017-07-01T00:00:00+00:00', 201707],
            'UTC end of month' => ['2017-07-31T23:59:59+00:00', 201707],
            'JST cross-month boundary backwards' => ['2017-08-01T08:00:00+09:00', 201707],
            'JST cross-month boundary forwards' => ['2017-08-01T10:00:00+09:00', 201708],
            'with PostgreSQL +00 offset' => ['2017-07-21 04:00:00+00', 201707],
            'two-digit year padding' => ['2099-01-01T00:00:00+00:00', 209901],
        ];
    }

    public function testFromDateTimeUsesUtcWallClock(): void
    {
        $dt = new DateTimeImmutable('2017-08-01T10:00:00', new DateTimeZone('Asia/Tokyo'));
        $this->assertSame(201708, YearMonth::fromDateTime($dt));

        $dt = new DateTimeImmutable('2017-08-01T08:00:00', new DateTimeZone('Asia/Tokyo'));
        $this->assertSame(201707, YearMonth::fromDateTime($dt));
    }
}
