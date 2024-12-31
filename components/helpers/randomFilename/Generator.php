<?php

/**
 * @copyright Copyright (C) 2021-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\randomFilename;

use ParagonIE\ConstantTime\Base32;

use function chr;
use function implode;
use function max;
use function min;
use function ord;
use function random_bytes;
use function substr;
use function trim;
use function vsprintf;

class Generator
{
    public static function generate(string $ext, int $level): string
    {
        return static::formatFileName(
            static::generateUUIDv4Binary(),
            $ext,
            $level,
        );
    }

    public static function generateUUIDv4Binary(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((0x04 << 4) | (ord($bytes[6]) & 0x0f));
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
        return $bytes;
    }

    public static function formatFileName(string $binary, string $ext, int $level): string
    {
        $fileName = static::formatFileNameFlat($binary, $ext);
        $level = min(max(0, $level), 10); // because of b32'ed UUID length = 26
        if ($level > 0) {
            $parts = [];
            for ($i = 0; $i < $level; ++$i) {
                $parts[] = substr($fileName, $i * 2, 2);
            }
            $parts[] = $fileName;
            $fileName = implode('/', $parts);
        }
        return $fileName;
    }

    public static function formatFileNameFlat(string $binary, string $ext): string
    {
        $ext = trim((string)$ext);
        return vsprintf('%s%s', [
            Base32::encodeUnpadded($binary),
            $ext !== '' ? '.' . $ext : '',
        ]);
    }
}
