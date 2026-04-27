<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use DomainException;
use app\components\helpers\Color;

class ColorTest extends Unit
{
    /**
     * @dataProvider hueDataProvider
     */
    public function testGetHueFromRGB(int $r, int $g, int $b, int $expected): void
    {
        $this->assertSame($expected, Color::getHueFromRGB($r, $g, $b));
    }

    public function hueDataProvider(): array
    {
        return [
            'pure red' => [255, 0, 0, 0],
            'pure yellow' => [255, 255, 0, 60],
            'pure green' => [0, 255, 0, 120],
            'pure cyan' => [0, 255, 255, 180],
            'pure blue' => [0, 0, 255, 240],
            'pure magenta' => [255, 0, 255, 300],
            'black is zero' => [0, 0, 0, 0],
            'white is zero' => [255, 255, 255, 0],
            'gray is zero' => [128, 128, 128, 0],
        ];
    }

    public function testHueRange(): void
    {
        // Verify that the result is normalized into [0, 359].
        for ($r = 0; $r <= 255; $r += 51) {
            for ($g = 0; $g <= 255; $g += 51) {
                for ($b = 0; $b <= 255; $b += 51) {
                    $hue = Color::getHueFromRGB($r, $g, $b);
                    $this->assertGreaterThanOrEqual(0, $hue);
                    $this->assertLessThan(360, $hue);
                }
            }
        }
    }

    public function testGetYUVFromRGBBlack(): void
    {
        [$y, $u, $v] = Color::getYUVFromRGB(0, 0, 0);
        $this->assertEqualsWithDelta(0.0, $y, 1e-9);
        $this->assertEqualsWithDelta(0.0, $u, 1e-9);
        $this->assertEqualsWithDelta(0.0, $v, 1e-9);
    }

    public function testGetYUVFromRGBWhiteHasFullLuminance(): void
    {
        // Y coefficients sum to 1.0 (0.299 + 0.587 + 0.114).
        [$y] = Color::getYUVFromRGB(255, 255, 255);
        $this->assertEqualsWithDelta(1.0, $y, 1e-9);
    }

    public function testGetYUVFromRGBLuminanceOrdering(): void
    {
        // Luminance (Y) should be larger for green than for blue at full saturation.
        [$yR] = Color::getYUVFromRGB(255, 0, 0);
        [$yG] = Color::getYUVFromRGB(0, 255, 0);
        [$yB] = Color::getYUVFromRGB(0, 0, 255);
        $this->assertGreaterThan($yR, $yG);
        $this->assertGreaterThan($yB, $yR);
    }

    public function testGetYUVThrowsOnOutOfRange(): void
    {
        $this->expectException(DomainException::class);
        Color::getYUVFromRGB(-1, 0, 0);
    }
}
