<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Primal\Color\HSVColor;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\components\helpers\Resource;

class GraphIconController extends Controller
{
    public function actionGenerate($inFile, $outDir)
    {
        if (!$inImg = imagecreatefrompng($inFile)) {
            echo "Could not read {$inFile} as a PNG image.\n";
            return 1;
        }
        for ($hue = 0; $hue < 360; $hue += 2) {
            $outFile = "{$outDir}/{$hue}.png";
            echo "Creating {$outFile}\n";
            $tmpFile = new Resource(tempnam(sys_get_temp_dir(), 'graphicon-'), 'unlink');
            if (!$this->generate($inImg, $tmpFile->get(), $hue, 0.8, 0.8)) {
                return 1;
            }
            $cmdline = sprintf(
                '/usr/bin/env %s -rem allb -l 9 -q %s %s',
                escapeshellarg('pngcrush'),
                escapeshellarg($tmpFile->get()),
                escapeshellarg($outFile)
            );
            $lines = $status = null;
            exec($cmdline, $lines, $status);
            if ($status !== 0) {
                echo "Could not optimize new PNG image file.\n";
                return 1;
            }
        }
    }

    protected function generate($inImg, $outPath, $h, $s, $v)
    {
        $hsv = new HSVColor((int)round($h), (int)round($s * 100), (int)round($v * 100));
        $rgb = $hsv->toRGB();
        $color = ((int)$rgb->red << 16) | ((int)$rgb->green << 8) | (int)$rgb->blue;
        $width = imagesx($inImg);
        $height = imagesy($inImg);
        if (!$img = imagecreatetruecolor($width, $height)) {
            echo "Could not create new true colored image.\n";
            return false;
        }
        imagealphablending($img, false);
        imagesavealpha($img, true);
        for ($y = 0; $y < $height; ++$y) {
            for ($x = 0; $x < $width; ++$x) {
                $alpha = imagecolorat($inImg, $x, $y) & 0x7f000000;
                $newColor = $alpha | $color;
                imagesetpixel($img, $x, $y, $newColor);
            }
        }
        if (!imagepng($img, $outPath)) {
            echo "Could not save new PNG image file.\n";
            return false;
        }
        return true;
    }
}
