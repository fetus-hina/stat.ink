<?php

declare(strict_types=1);

namespace tests\models;

use Codeception\Test\Unit;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Spl3YearMonthForm;

final class Spl3YearMonthFormTest extends Unit
{
    public function testRequired(): void
    {
        $form = $this->createForm('', '');
        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors('year'));
        $this->assertTrue($form->hasErrors('month'));
    }

    public function testNonInteger(): void
    {
        $form = $this->createForm('a', 'b');
        $this->assertFalse($form->validate());
        $this->assertTrue($form->hasErrors('year'));
        $this->assertTrue($form->hasErrors('month'));
    }

    public function testValidDate(): void
    {
        $this->assertTrue($this->createForm(2023, 6)->validate());
        $this->assertTrue($this->createForm('2023', '6')->validate());
    }

    public function testOutOfRangeMonth(): void
    {
        $this->assertFalse($this->createForm(2023, 0)->validate());
        $this->assertFalse($this->createForm(2023, 13)->validate());
    }

    public function testLowerBound(): void
    {
        // Splatfest World Premiere: 2022-08-27
        $this->assertTrue($this->createForm(2022, 8)->validate());
        $this->assertFalse($this->createForm(2022, 7)->validate());
        $this->assertFalse($this->createForm(2021, 12)->validate());
    }

    public function testUpperBound(): void
    {
        $this->assertTrue($this->createForm(2025, 4)->validate());
        $this->assertFalse($this->createForm(2025, 5)->validate());
        $this->assertFalse($this->createForm(2026, 1)->validate());
    }

    public function testUpperBoundDependsOnTimeZone(): void
    {
        // 2025-04-30T20:00:00Z is 2025-05-01T05:00:00 in Asia/Tokyo
        $now = new DateTimeImmutable('2025-04-30T20:00:00+00:00');

        $utc = $this->createForm(2025, 5, 'Etc/UTC', $now);
        $this->assertFalse($utc->validate());

        $tokyo = $this->createForm(2025, 5, 'Asia/Tokyo', $now);
        $this->assertTrue($tokyo->validate());
    }

    public function testMonthRange(): void
    {
        $form = $this->createForm(2024, 2, 'Asia/Tokyo');
        $this->assertTrue($form->validate());

        $this->assertSame(
            '2024-02-01T00:00:00+09:00',
            $form->getFirstDayOfMonth()->format(DATE_ATOM),
        );
        $this->assertSame(
            '2024-03-01T00:00:00+09:00',
            $form->getFirstDayOfNextMonth()->format(DATE_ATOM),
        );
    }

    public function testMonthRangeAcrossYear(): void
    {
        $form = $this->createForm(2023, 12, 'Etc/UTC');
        $this->assertTrue($form->validate());

        $this->assertSame(
            '2023-12-01T00:00:00+00:00',
            $form->getFirstDayOfMonth()->format(DATE_ATOM),
        );
        $this->assertSame(
            '2024-01-01T00:00:00+00:00',
            $form->getFirstDayOfNextMonth()->format(DATE_ATOM),
        );
    }

    public function testPrevAndNextMonth(): void
    {
        $form = $this->createForm(2024, 1);
        $this->assertTrue($form->validate());
        $this->assertSame('2023-12', $form->getPrevMonth()?->format('Y-m'));
        $this->assertSame('2024-02', $form->getNextMonth()?->format('Y-m'));
    }

    public function testPrevMonthAtLowerBound(): void
    {
        $form = $this->createForm(2022, 8);
        $this->assertTrue($form->validate());
        $this->assertNull($form->getPrevMonth());
        $this->assertSame('2022-09', $form->getNextMonth()?->format('Y-m'));
    }

    public function testNextMonthAtUpperBound(): void
    {
        $form = $this->createForm(2025, 4);
        $this->assertTrue($form->validate());
        $this->assertSame('2025-03', $form->getPrevMonth()?->format('Y-m'));
        $this->assertNull($form->getNextMonth());
    }

    private function createForm(
        int|string $year,
        int|string $month,
        string $timeZone = 'Etc/UTC',
        ?DateTimeImmutable $now = null,
    ): Spl3YearMonthForm {
        $tz = new DateTimeZone($timeZone);
        $form = Yii::createObject(Spl3YearMonthForm::class);
        $form->timeZone = $tz;
        $form->now = ($now ?? new DateTimeImmutable('2025-04-15T12:00:00+00:00'))->setTimezone($tz);
        $form->year = $year;
        $form->month = $month;
        return $form;
    }
}
