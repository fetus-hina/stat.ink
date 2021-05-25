<?php

declare(strict_types=1);

namespace tests\models;

use Codeception\Test\Unit;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Spl2YearMonthForm;

final class Spl2YearMonthFormTest extends Unit
{
    public function testRequired(): void
    {
        $form = Yii::createObject([
            'class' => Spl2YearMonthForm::class,
            'year' => '',
            'month' => '',
        ]);
        $this->assertFalse($form->validate());

        $this->assertTrue($form->hasErrors('year'));
        $this->assertTrue($form->hasErrors('month'));

        $this->assertEquals('Year cannot be blank.', $form->getFirstError('year'));
        $this->assertEquals('Month cannot be blank.', $form->getFirstError('month'));
    }

    public function testNonInteger(): void
    {
        $form = Yii::createObject([
            'class' => Spl2YearMonthForm::class,
            'year' => 'a',
            'month' => 'b',
        ]);
        $this->assertFalse($form->validate());

        $this->assertTrue($form->hasErrors('year'));
        $this->assertTrue($form->hasErrors('month'));

        $this->assertEquals('Year must be an integer.', $form->getFirstError('year'));
        $this->assertEquals('Month must be an integer.', $form->getFirstError('month'));
    }

    public function testValidDate(): void
    {
        $form = Yii::createObject([
            'class' => Spl2YearMonthForm::class,
            'year' => 2020,
            'month' => 6,
        ]);
        $this->assertTrue($form->validate());

        $form->year = '2020';
        $form->month = '6';
        $this->assertTrue($form->validate());
    }

    public function testOutOfRangeMonth(): void
    {
        $form = Yii::createObject([
            'class' => Spl2YearMonthForm::class,
            'year' => 2020,
            'month' => 0,
        ]);
        $this->assertFalse($form->validate());
        $this->assertEquals('Month must be no less than 1.', $form->getFirstError('month'));

        $form->month = 13;
        $this->assertFalse($form->validate());
        $this->assertEquals('Month must be no greater than 12.', $form->getFirstError('month'));
    }

    public function testReleaseDate(): void
    {
        $form = Yii::createObject([
            'class' => Spl2YearMonthForm::class,
            'year' => 2017,
            'month' => 7,
        ]);
        $this->assertTrue($form->validate());

        $form->month = 6;
        $this->assertFalse($form->validate());
    }

    public function testFutureDate(): void
    {
        $form = Yii::createObject(Spl2YearMonthForm::class);
        $form->timeZone = new DateTimeZone('Etc/UTC');
        $form->now = new DateTimeImmutable('2020-02-01T00:00:00+00:00', $form->timeZone);
        $form->year = 2020;
        $form->month = 1;
        $this->assertTrue($form->validate());

        $form->month = 2;
        $this->assertTrue($form->validate());

        $form->month = 3;
        $this->assertFalse($form->validate());
    }
}
