<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use Exception;
use Yii;

class ImageConverter
{
    const OUT_WIDTH = 640;
    const OUT_HEIGHT = 360;

    const JPEG_QUALITY = 90;

    public static function convert($binary, $outPathJpeg, $blackoutPosList = false, $outPathArchivePng = null)
    {
        if (!is_array($blackoutPosList)) {
            $blackoutPosList = [];
        }
        if (!$tmpName = self::convertImpl($binary, $blackoutPosList)) {
            return false;
        }
        $leptonMode = Yii::$app->params['lepton']['mode'] ?? 'none';
        if (!self::copyJpeg($tmpName->get(), $outPathJpeg)) {
            @unlink($outPathJpeg);
            return false;
        }
        if ($leptonMode === 'both' || $leptonMode === 'only') {
            if (self::copyLepton($outPathJpeg)) {
                if ($leptonMode === 'only') {
                    @unlink($outPathJpeg);
                }
            }
        }
        if ($outPathArchivePng !== null) {
            $in = new Resource(@imagecreatefromstring($binary), 'imagedestroy');
            if ($in->get()) {
                self::mkdir(dirname($outPathArchivePng));
                imagepng($in->get(), $outPathArchivePng, 3, PNG_NO_FILTER);
            }
        }
        return true;
    }

    protected static function convertImpl($binary, array $blackoutPosList)
    {
        try {
            $in = new Resource(@imagecreatefromstring($binary), 'imagedestroy');
            if (!$in->get()) {
                throw new Exception();
            }
            $out = new Resource(imagecreatetruecolor(self::OUT_WIDTH, self::OUT_HEIGHT), 'imagedestroy');
            if (!$out->get()) {
                throw new Exception();
            }
            $inW = imagesx($in->get());
            $inH = imagesy($in->get());
            if ($inW < 100 || $inH < 100) {
                throw new Exception();
            }
            $scale = min(self::OUT_WIDTH / $inW, self::OUT_HEIGHT / $inH);
            $cpW = (int)round($inW * $scale);
            $cpH = (int)round($inH * $scale);
            $cpX = (int)round(self::OUT_WIDTH / 2 - $cpW / 2);
            $cpY = (int)round(self::OUT_HEIGHT / 2 - $cpH / 2);
            imagealphablending($out->get(), false);
            imagefill($out->get(), 0, 0, 0xffffff);
            imagealphablending($out->get(), true);
            imagecopyresampled(
                $out->get(),
                $in->get(),
                $cpX,
                $cpY,
                0,
                0,
                $cpW,
                $cpH,
                $inW,
                $inH
            );
            if ($blackoutPosList) {
                for ($i = 0; $i < 8; ++$i) {
                    if (!in_array($i + 1, $blackoutPosList)) {
                        continue;
                    }

                    $y = ($i < 4 ? 50 : 215) + (($i % 4) * 33);
                    imagefilledrectangle(
                        $out->get(),
                        406, //x1
                        $y,
                        406 + 86,
                        $y + 19,
                        0x000000
                    );
                }
            }
            $tmpName = new Resource(tempnam(sys_get_temp_dir(), 'statink-'), 'unlink');
            imagepng($out->get(), $tmpName->get(), 9, PNG_ALL_FILTERS);
            return $tmpName;
        } catch (Exception $e) {
        }
        return false;
    }

    protected static function copyJpeg($inPath, $outPath)
    {
        self::mkdir(dirname($outPath));
        $cmdlines = [
            sprintf(
                '/usr/bin/env %s %s -quality %d %s',
                escapeshellarg('convert'),
                escapeshellarg($inPath),
                self::JPEG_QUALITY,
                escapeshellarg($outPath)
            ),
            sprintf(
                '/usr/bin/env %s --quiet --strip-all %s',
                escapeshellarg('jpegoptim'),
                escapeshellarg($outPath)
            ),
        ];
        foreach ($cmdlines as $cmdline) {
            $lines = [];
            $status = -1;
            @exec($cmdline, $lines, $status);
            if ($status != 0) {
                @unlink($outPath);
                return false;
            }
        }
        return true;
    }

    protected static function copyLepton($jpegPath)
    {
        $binPath = Yii::$app->params['lepton']['bin'] ?? false;
        if (!$binPath || !@file_exists($binPath) || !is_executable($binPath)) {
            return false;
        }

        $leptonPath = preg_replace('/\.jpg$/', '.lep', $jpegPath);
        self::mkdir(dirname($leptonPath));
        $cmdline = sprintf(
            '/usr/bin/env %s %s %s',
            escapeshellarg($binPath),
            escapeshellarg($jpegPath),
            escapeshellarg($leptonPath)
        );
        $lines = [];
        $status = -1;
        @exec($cmdline, $lines, $status);
        if ($status != 0 || @filesize($leptonPath) < 100) {
            @unlink($leptonPath);
            return false;
        }
        @chmod($leptonPath, 0644);
        return true;
    }

    private static function mkdir($dir)
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
