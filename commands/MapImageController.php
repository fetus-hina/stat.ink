<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\components\helpers\Resource;
use app\models\Map;

class MapImageController extends Controller
{
    public $inDir           = '@app/resources/maps/daytime';
    public $outDirBlur      = '@app/resources/maps/daytime-blur';
    public $outDirGrayBlur  = '@app/resources/maps/gray-blur';

    public function actionGenerate()
    {
        foreach (Map::find()->asArray()->all() as $map) {
            printf("[%s] %s\n", $map['key'], $map['name']);
            $inFile = Yii::getAlias($this->inDir) . '/' . $map['key'] . '.jpg';

            echo "  Blur...\n";
            $outFileBlur = Yii::getAlias($this->outDirBlur) . '/' . $map['key'] . '.jpg';
            if (!$this->generateBlur($inFile, $outFileBlur)) {
                return false;
            }

            echo "  Grayscale...\n";
            $outFileGrayBlur = Yii::getAlias($this->outDirGrayBlur) . '/' . $map['key'] . '.jpg';
            if (!$this->generateGrayscale($outFileBlur, $outFileGrayBlur)) {
                return false;
            }
        }
        echo "done.\n";
    }

    private function generateBlur($inFile, $outFile)
    {
        $img = new Resource(imagecreatefromjpeg($inFile), 'imagedestroy');
        if (!$img->get()) {
            return false;
        }
        $matrix = [
            [1, 2, 1],
            [2, 4, 2],
            [1, 2, 1],
        ];
        for ($i = 0; $i < 5; ++$i) {
            if (!imageconvolution($img->get(), $matrix, 16, 0)) {
                return false;
            }
        }
        if (!file_exists(dirname($outFile))) {
            if (!mkdir(dirname($outFile), 0755, true)) {
                return false;
            }
        }
        if (!imagejpeg($img->get(), $outFile, 98)) {
            return false;
        }
        $cmdline = sprintf('/usr/bin/env jpegoptim -qso %s', escapeshellarg($outFile));
        $lines = $status = null;
        exec($cmdline, $lines, $status);
        return $status == 0;
    }

    private function generateGrayscale($inFile, $outFile)
    {
        $img = new Resource(imagecreatefromjpeg($inFile), 'imagedestroy');
        if (!$img->get()) {
            return false;
        }
        $w = imagesx($img->get());
        $h = imagesy($img->get());
        for ($y = 0; $y < $h; ++$y) {
            for ($x = 0; $x < $w; ++$x) {
                $c = imagecolorat($img->get(), $x, $y);
                $r = ($c & 0xff0000) >> 16;
                $g = ($c & 0x00ff00) >>  8;
                $b = ($c & 0x0000ff) >>  0;
                $_ = min(255, (int)round(0.299 * $r + 0.587 * $g + 0.114 * $b));
                imagesetpixel($img->get(), $x, $y, $_ * 0x10101);
            }
        }
        if (!file_exists(dirname($outFile))) {
            if (!mkdir(dirname($outFile), 0755, true)) {
                return false;
            }
        }
        if (!imagejpeg($img->get(), $outFile, 98)) {
            return false;
        }
        $cmdline = sprintf('/usr/bin/env jpegoptim -qso %s', escapeshellarg($outFile));
        $lines = $status = null;
        exec($cmdline, $lines, $status);
        return $status == 0;
    }
}
