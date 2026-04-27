<?php

declare(strict_types=1);

namespace tests\helpers;

use ArrayObject;
use Codeception\Test\Unit;
use Stringable;
use TypeError;
use app\components\helpers\TypeHelper;
use stdClass;

class TypeHelperTest extends Unit
{
    public function testStringFromString(): void
    {
        $this->assertSame('hello', TypeHelper::string('hello'));
    }

    public function testStringFromStringable(): void
    {
        $obj = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable';
            }
        };
        $this->assertSame('stringable', TypeHelper::string($obj));
    }

    public function testStringRejectsNonString(): void
    {
        // Numbers must NOT be silently coerced (use stringOrNull for that).
        $this->expectException(TypeError::class);
        TypeHelper::string(123);
    }

    public function testStringOrNullCoercesScalars(): void
    {
        $this->assertSame('123', TypeHelper::stringOrNull(123));
        $this->assertSame('1.5', TypeHelper::stringOrNull(1.5));
        $this->assertSame('1', TypeHelper::stringOrNull(true));
        $this->assertNull(TypeHelper::stringOrNull(null));
        $this->assertNull(TypeHelper::stringOrNull([]));
        $this->assertNull(TypeHelper::stringOrNull(new stdClass()));
    }

    public function testIntFromInt(): void
    {
        $this->assertSame(42, TypeHelper::int(42));
    }

    public function testIntFromNumericString(): void
    {
        $this->assertSame(42, TypeHelper::int('42'));
        $this->assertSame(-1, TypeHelper::int('-1'));
    }

    public function testIntRejectsNonInteger(): void
    {
        $this->expectException(TypeError::class);
        TypeHelper::int('not-a-number');
    }

    public function testIntOrNullReturnsNullForUnparseable(): void
    {
        $this->assertNull(TypeHelper::intOrNull(null));
        $this->assertNull(TypeHelper::intOrNull('abc'));
        $this->assertNull(TypeHelper::intOrNull('1.5'));
        $this->assertSame(7, TypeHelper::intOrNull('7'));
        $this->assertSame(7, TypeHelper::intOrNull(7));
    }

    public function testFloatFromFloatAndString(): void
    {
        $this->assertSame(1.5, TypeHelper::float(1.5));
        $this->assertSame(1.5, TypeHelper::float('1.5'));
    }

    public function testFloatRejectsNonFloat(): void
    {
        $this->expectException(TypeError::class);
        TypeHelper::float('not-a-number');
    }

    public function testFloatOrNull(): void
    {
        $this->assertNull(TypeHelper::floatOrNull(null));
        $this->assertNull(TypeHelper::floatOrNull('abc'));
        $this->assertSame(2.5, TypeHelper::floatOrNull('2.5'));
        $this->assertSame(2.5, TypeHelper::floatOrNull(2.5));
    }

    public function testUrlValid(): void
    {
        $this->assertSame(
            'https://example.com/path',
            TypeHelper::url('https://example.com/path'),
        );
    }

    public function testUrlRejectsPathlessUrl(): void
    {
        // FILTER_FLAG_PATH_REQUIRED rejects host-only URLs.
        $this->expectException(TypeError::class);
        TypeHelper::url('https://example.com');
    }

    public function testUrlRejectsInvalidString(): void
    {
        $this->expectException(TypeError::class);
        TypeHelper::url('not a url');
    }

    public function testArrayPassesThrough(): void
    {
        $this->assertSame([1, 2, 3], TypeHelper::array([1, 2, 3]));
    }

    public function testArrayRejectsNonArray(): void
    {
        $this->expectException(TypeError::class);
        TypeHelper::array('not array');
    }

    public function testInstanceOfReturnsObjectOnMatch(): void
    {
        $obj = new ArrayObject();
        $this->assertSame($obj, TypeHelper::instanceOf($obj, ArrayObject::class));
    }

    public function testInstanceOfRejectsMismatch(): void
    {
        $this->expectException(TypeError::class);
        TypeHelper::instanceOf(new stdClass(), ArrayObject::class);
    }
}
