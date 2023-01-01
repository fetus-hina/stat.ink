<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use function implode;
use function sprintf;

final class UuidRegexp
{
    public static function get(bool $wrap = false, bool $acceptNull = false): string
    {
        if ($wrap) {
            return sprintf('/^%s$/i', self::get(false, $acceptNull));
        }

        return $acceptNull
            ? sprintf('%s|%s', self::getRfc4122(), self::getNull())
            : self::getRfc4122();
    }

    private static function getRfc4122(): string
    {
        return implode('-', [
            '[0-9a-f]{8}',
            '[0-9a-f]{4}',
            '[1345][0-9a-f]{3}', // version
            '[89ab][0-9a-f]{3}', // variant
            '[0-9a-f]{12}',
        ]);
    }

    private static function getNull(): string
    {
        return '0{8}-0{4}-0{4}-0{4}-0{12}';
    }
}
