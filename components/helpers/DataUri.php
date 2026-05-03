<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use function base64_decode;
use function preg_match;
use function strtolower;

final class DataUri
{
    /**
     * Parses a base64-encoded `data:` URI into its MIME type and decoded binary.
     *
     * @return array{string, string}|null `[mime_type, binary]`, or `null` if the input is not a
     *                                    well-formed base64-encoded data URI.
     */
    public static function parse(string $uri): ?array
    {
        if (preg_match('#^data:([a-z0-9.+/-]+);base64,([A-Za-z0-9+/=]*)$#i', $uri, $match) !== 1) {
            return null;
        }

        $binary = base64_decode($match[2], true);
        if ($binary === false) {
            return null;
        }

        return [strtolower($match[1]), $binary];
    }
}
