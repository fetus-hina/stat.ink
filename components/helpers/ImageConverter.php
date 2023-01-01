<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\helpers;

use Exception;
use Throwable;
use Yii;

use function chmod;
use function dirname;
use function escapeshellarg;
use function exec;
use function file_exists;
use function filesize;
use function imagealphablending;
use function imagecopyresampled;
use function imagecreatefromstring;
use function imagecreatetruecolor;
use function imagefill;
use function imagefilledrectangle;
use function imagepng;
use function imagesx;
use function imagesy;
use function in_array;
use function is_array;
use function is_executable;
use function max;
use function min;
use function mkdir;
use function preg_replace;
use function round;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const PNG_ALL_FILTERS;
use const PNG_NO_FILTER;

class ImageConverter
{
    public const OUT_WIDTH = 1280;
    public const OUT_HEIGHT = 720;
    public const JPEG_QUALITY = 85;

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
            $inW = imagesx($in->get());
            $inH = imagesy($in->get());
            if ($inW < 100 || $inH < 100) {
                throw new Exception();
            }

            if ($inW > static::OUT_WIDTH || $inH > static::OUT_HEIGHT) {
                $scale = min(static::OUT_WIDTH / $inW, static::OUT_HEIGHT / $inH);
                $cpW = (int)round($inW * $scale);
                $cpH = (int)round($inH * $scale);
                $canvasW = static::OUT_WIDTH;
                $canvasH = static::OUT_HEIGHT;
            } else {
                $scale = 1.0;
                $cpW = $inW;
                $cpH = $inH;
                $canvasW = max($inW, (int)round($inH * 16 / 9));
                $canvasH = max($inH, (int)round($inW * 9 / 16));
            }
            $cpX = (int)round($canvasW / 2 - $cpW / 2);
            $cpY = (int)round($canvasH / 2 - $cpH / 2);
            $out = new Resource(imagecreatetruecolor($canvasW, $canvasH), 'imagedestroy');
            if (!$out->get()) {
                throw new Exception();
            }
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
                $inH,
            );
            if ($blackoutPosList) {
                for ($i = 0; $i < 8; ++$i) {
                    if (!in_array($i + 1, $blackoutPosList)) {
                        continue;
                    }

                    $y = ($i < 4 ? 100 : 430) + (($i % 4) * 66);
                    imagefilledrectangle(
                        $out->get(),
                        812, //x1
                        $y,
                        812 + 172,
                        $y + 38,
                        0x000000,
                    );
                }
            }
            $tmpName = new Resource(tempnam(sys_get_temp_dir(), 'statink-'), 'unlink');
            imagepng($out->get(), $tmpName->get(), 9, PNG_ALL_FILTERS);
            return $tmpName;
        } catch (Throwable $e) {
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
                escapeshellarg($outPath),
            ),
            sprintf(
                '/usr/bin/env %s --quiet --strip-all %s',
                escapeshellarg('jpegoptim'),
                escapeshellarg($outPath),
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
            escapeshellarg($leptonPath),
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
