<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\randomFilename;

use Base32\Base32;

use function chr;
use function implode;
use function max;
use function min;
use function ord;
use function random_bytes;
use function rtrim;
use function strtolower;
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

        $base32 = rtrim(Base32::encode($binary), '=');
        $base32 = strtolower($base32);

        return vsprintf('%s%s', [
            $base32,
            $ext !== ''
              ? '.' . $ext
              : '',
        ]);
    }
}
