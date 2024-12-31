<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use DomainException;

use function max;
use function min;
use function round;

final class Color
{
    /**
     * @param int<0, 255> $r
     * @param int<0, 255> $g
     * @param int<0, 255> $b
     * @return int<0, 359>
     */
    public static function getHueFromRGB(int $r, int $g, int $b): int
    {
        if ($r === $g && $g === $b) {
            return 0;
        }
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        switch ($max) {
            case $r:
                $hue = 60 * ($g - $b) / ($max - $min);
                break;

            case $g:
                $hue = 60 * (($b - $r) / ($max - $min)) + 120;
                break;

            default:
                $hue = 60 * (($r - $g) / ($max - $min)) + 240;
        }

        $hue = (int)round($hue);
        while ($hue >= 360) {
            $hue -= 360;
        }
        while ($hue < 0) {
            $hue += 360;
        }
        return $hue;
    }

    /**
     * @param int<0, 255> $r
     * @param int<0, 255> $g
     * @param int<0, 255> $b
     * @return array{float, float, float}
     */
    public static function getYUVFromRGB(int $r, int $g, int $b): array
    {
        if (
            $r < 0 || $r > 255 ||
            $g < 0 || $g > 255 ||
            $b < 0 || $b > 255
        ) {
            throw new DomainException();
        }

        $rs = $r / 255.0;
        $gs = $g / 255.0;
        $bs = $b / 255.0;

        return [
            $rs * 0.299 + $gs * 0.587 + $bs * 0.114,
            $rs * -0.14713 + $gs * -0.28886 + $bs * 0.436,
            $rs * 0.615 + $gs * -0.51499 + $bs * -0.0001,
        ];
    }
}
