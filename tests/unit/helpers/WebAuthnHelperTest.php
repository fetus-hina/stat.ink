<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use InvalidArgumentException;
use app\components\helpers\WebAuthnHelper;

use function hex2bin;
use function str_repeat;

class WebAuthnHelperTest extends Unit
{
    /**
     * @dataProvider binaryToUuidStringProvider
     */
    public function testBinaryToUuidString(string $expected, string $hex): void
    {
        $this->assertSame(
            $expected,
            WebAuthnHelper::binaryToUuidString((string)hex2bin($hex)),
        );
    }

    public static function binaryToUuidStringProvider(): array
    {
        return [
            // Authenticators without attestation (e.g., "none" format) report the nil AAGUID
            'nil' => [
                '00000000-0000-0000-0000-000000000000',
                '00000000000000000000000000000000',
            ],
            // Google Password Manager
            'regular' => [
                'ea9b8d66-4d01-1d21-3ce4-b6b48cb575d4',
                'EA9B8D664D011D213CE4B6B48CB575D4',
            ],
            // An AAGUID is just 16 bytes and need not follow the RFC 9562 variant/version bits
            'non-rfc variant' => [
                'ffffffff-ffff-ffff-ffff-ffffffffffff',
                'ffffffffffffffffffffffffffffffff',
            ],
        ];
    }

    /**
     * @dataProvider invalidLengthProvider
     */
    public function testBinaryToUuidStringRejectsInvalidLength(string $binary): void
    {
        $this->expectException(InvalidArgumentException::class);
        WebAuthnHelper::binaryToUuidString($binary);
    }

    public static function invalidLengthProvider(): array
    {
        return [
            'empty' => [''],
            '15 bytes' => [str_repeat("\0", 15)],
            '17 bytes' => [str_repeat("\0", 17)],
        ];
    }
}
