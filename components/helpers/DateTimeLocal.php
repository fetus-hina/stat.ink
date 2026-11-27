<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use function is_string;
use function preg_match;
use function vsprintf;

/**
 * Converts between the values of `<input type="datetime-local">` ("YYYY-MM-DDTHH:mm[:ss]")
 * and the values of the filter forms ("YYYY-MM-DD HH:mm:ss")
 */
final class DateTimeLocal
{
    /**
     * Converts a value submitted from a datetime-local input to the form value.
     * Other values are returned as is, so that the validators handle them.
     */
    public static function toFormValue(mixed $value): mixed
    {
        if (
            !is_string($value) ||
            !preg_match('/^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2})(:\d{2})?$/', $value, $match)
        ) {
            return $value;
        }

        return vsprintf('%s %s%s', [
            $match[1],
            $match[2],
            ($match[3] ?? '') !== '' ? $match[3] : ':00',
        ]);
    }

    /**
     * Converts a form value to the value of a datetime-local input.
     * Returns an empty string for a value that cannot be shown in the input.
     */
    public static function toInputValue(mixed $value): string
    {
        if (
            !is_string($value) ||
            !preg_match(
                '/^(\d{4})-(\d{1,2})-(\d{1,2})[ T](\d{1,2}):(\d{1,2})(?::(\d{1,2}))?$/',
                $value,
                $match,
            )
        ) {
            return '';
        }

        return vsprintf('%04d-%02d-%02dT%02d:%02d:%02d', [
            (int)$match[1],
            (int)$match[2],
            (int)$match[3],
            (int)$match[4],
            (int)$match[5],
            (int)($match[6] ?? 0),
        ]);
    }
}
