<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\validators;

use Override;
use yii\validators\FilterValidator;

use function idn_to_ascii;
use function preg_replace_callback;
use function str_contains;
use function strtolower;

class IdnToPunycodeFilterValidator extends FilterValidator
{
    #[Override]
    public function init(): void
    {
        $this->filter = function ($value) {
            if (!str_contains($value, '/')) {
                return self::toAscii($value);
            }
            if (str_contains($value, '//')) {
                return preg_replace_callback(
                    '!(?<=//)([^/:]+)!',
                    fn ($match) => self::toAscii($match[1]),
                    $value,
                    1,
                );
            }
            return preg_replace_callback(
                '!^([^/:]+)!',
                fn ($match) => self::toAscii($match[1]),
                $value,
                1,
            );
        };
        parent::init();
    }

    private static function toAscii(string $host): string
    {
        $ascii = idn_to_ascii($host);
        return strtolower($ascii === false ? $host : $ascii);
    }
}
