<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\UuidRegexp;

use function preg_match;

class UuidRegexpTest extends Unit
{
    public function testWrappedReturnsAnchoredRegex(): void
    {
        $regex = UuidRegexp::get(true);
        $this->assertSame(1, preg_match($regex, '550e8400-e29b-41d4-a716-446655440000'));
        // Wrapped regex should not match if there is leading or trailing junk.
        $this->assertSame(0, preg_match($regex, 'x550e8400-e29b-41d4-a716-446655440000'));
        $this->assertSame(0, preg_match($regex, '550e8400-e29b-41d4-a716-446655440000x'));
    }

    public function testValidVersionsAreAccepted(): void
    {
        $regex = UuidRegexp::get(true);
        // Versions 1, 3, 4, 5 should all match (the rfc4122 part allows [13-8] for the version nybble).
        $this->assertSame(1, preg_match($regex, 'd1d2d3d4-d5d6-1abc-8def-d7d8d9dadbdc'));
        $this->assertSame(1, preg_match($regex, 'd1d2d3d4-d5d6-3abc-9def-d7d8d9dadbdc'));
        $this->assertSame(1, preg_match($regex, 'd1d2d3d4-d5d6-4abc-adef-d7d8d9dadbdc'));
        $this->assertSame(1, preg_match($regex, 'd1d2d3d4-d5d6-5abc-bdef-d7d8d9dadbdc'));
    }

    public function testInvalidVersionAndVariantAreRejected(): void
    {
        $regex = UuidRegexp::get(true);
        // Version "2" is not in [13-8].
        $this->assertSame(0, preg_match($regex, 'd1d2d3d4-d5d6-2abc-8def-d7d8d9dadbdc'));
        // Variant "0" is not in [89ab].
        $this->assertSame(0, preg_match($regex, 'd1d2d3d4-d5d6-4abc-0def-d7d8d9dadbdc'));
    }

    public function testNullUuidRequiresAcceptNullFlag(): void
    {
        $null = '00000000-0000-0000-0000-000000000000';
        $strictRegex = UuidRegexp::get(true);
        $loose = UuidRegexp::get(true, true);

        $this->assertSame(0, preg_match($strictRegex, $null));
        $this->assertSame(1, preg_match($loose, $null));
    }

    public function testCaseInsensitive(): void
    {
        $regex = UuidRegexp::get(true);
        $this->assertSame(1, preg_match($regex, 'D1D2D3D4-D5D6-4ABC-ADEF-D7D8D9DADBDC'));
    }
}
