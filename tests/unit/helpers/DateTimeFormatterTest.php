<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use DateTimeZone;
use app\components\helpers\DateTimeFormatter;

class DateTimeFormatterTest extends Unit
{
    public function testIntegerUnixtimeUsesSecondPrecisionUtc(): void
    {
        // 2021-01-02T03:04:05Z
        $this->assertSame(
            '2021-01-02T03:04:05+00:00',
            DateTimeFormatter::unixTimeToString(1609556645),
        );
    }

    public function testFloatUnixtimeIncludesMicroseconds(): void
    {
        $result = DateTimeFormatter::unixTimeToString(1609556645.123456);
        $this->assertSame('2021-01-02T03:04:05.123456+00:00', $result);
    }

    public function testTimezoneOverrideIsApplied(): void
    {
        $tokyo = new DateTimeZone('Asia/Tokyo');
        $this->assertSame(
            '2021-01-02T12:04:05+09:00',
            DateTimeFormatter::unixTimeToString(1609556645, $tokyo),
        );
    }

    public function testJsonArrayShape(): void
    {
        $arr = DateTimeFormatter::unixTimeToJsonArray(1609556645);
        $this->assertSame(
            [
                'time' => 1609556645,
                'iso8601' => '2021-01-02T03:04:05+00:00',
            ],
            $arr,
        );
    }

    public function testJsonArrayDropsFractionalPartFromTime(): void
    {
        // The "time" field is documented as integer seconds even when the input is a float.
        $arr = DateTimeFormatter::unixTimeToJsonArray(1609556645.987);
        $this->assertSame(1609556645, $arr['time']);
        // unixTimeToJsonArray casts to int before formatting, so iso8601 has no fractional part.
        $this->assertSame('2021-01-02T03:04:05+00:00', $arr['iso8601']);
    }
}
