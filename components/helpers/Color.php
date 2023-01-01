<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use function max;
use function min;
use function round;

class Color
{
    public static function getHueFromRGB($r, $g, $b)
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
}
