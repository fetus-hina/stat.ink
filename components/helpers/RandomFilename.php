<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use Base32\Base32;

class RandomFilename
{
    public static function generate($ext = '')
    {
        // Generate UUIDv4
        $bytes = random_bytes(16);
        $bytes[6] = chr((0x04 << 4) | (ord($bytes[6]) & 0x0f));
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        $base32 = rtrim(Base32::encode($bytes), '=');
        $base32 = strtolower($base32);

        $filename = $base32 . (($ext != '') ? ('.' . $ext) : '');
        return $filename;
    }
}
