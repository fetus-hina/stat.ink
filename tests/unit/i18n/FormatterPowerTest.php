<?php

declare(strict_types=1);

namespace tests\i18n;

use Codeception\Test\Unit;
use app\components\i18n\Formatter;

class FormatterPowerTest extends Unit
{
    private function createFormatter(string $locale): Formatter
    {
        return new Formatter([
            'locale' => $locale,
            'nullDisplay' => '(null)',
        ]);
    }

    public function testDotDecimalLocalesHaveNoThousandSeparator(): void
    {
        $this->assertSame('1234.5', $this->createFormatter('ja-JP')->asPower(1234.5));
        $this->assertSame('1234.5', $this->createFormatter('en-US')->asPower(1234.5));
    }

    public function testCommaDecimalLocalesHaveNoThousandSeparator(): void
    {
        $this->assertSame('1234,5', $this->createFormatter('fr-FR')->asPower(1234.5));
        $this->assertSame('1234,5', $this->createFormatter('de-DE')->asPower(1234.5));
    }

    public function testDefaultDecimalsIsOne(): void
    {
        $f = $this->createFormatter('en-US');
        $this->assertSame('2000.0', $f->asPower(2000));
        $this->assertSame('2000.0', $f->asPower('2000'));
        $this->assertSame('2000.3', $f->asPower(2000.3));
    }

    public function testValueIsTruncatedNotRounded(): void
    {
        $f = $this->createFormatter('en-US');
        $this->assertSame('1234.5', $f->asPower(1234.56));
        $this->assertSame('1234.5', $f->asPower('1234.59'));
        $this->assertSame('2000.0', $f->asPower(2000.09));
        $this->assertSame('1234', $f->asPower(1234.9, 0));
    }

    public function testFloatingPointErrorDoesNotDropLastDigit(): void
    {
        $value = 2000.0;
        $value += 0.1;
        $value += 0.1;
        $value += 0.1; // 2000.2999999999997

        $f = $this->createFormatter('en-US');
        $this->assertSame('2000.3', $f->asPower($value));
        $this->assertSame('1234.5', $f->asPower(1234.5999));
    }

    public function testLongNumericStringIsNotGroupedNorRounded(): void
    {
        $f = $this->createFormatter('en-US');
        $this->assertSame('2345.6', $f->asPower('2345.6789012345678901'));
        $this->assertSame('12345.6', $f->asPower('12345.6789012345678901'));
    }

    public function testDecimalsCanBeSpecified(): void
    {
        $this->assertSame('1234', $this->createFormatter('en-US')->asPower(1234.5, 0));
        $this->assertSame('1234,56', $this->createFormatter('fr-FR')->asPower(1234.567, 2));
    }

    public function testNullReturnsNullDisplay(): void
    {
        $this->assertSame('(null)', $this->createFormatter('ja-JP')->asPower(null));
    }
}
