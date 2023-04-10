<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Stringable;
use TypeError;

use function filter_var;
use function is_float;
use function is_int;
use function is_object;
use function is_scalar;
use function is_string;

use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;

final class TypeHelper
{
    public static function string(mixed $value): string
    {
        return match (true) {
            is_string($value) => $value,
            $value instanceof Stringable => (string)$value,
            default => throw new TypeError('The value is not a string'),
        };
    }

    public static function stringOrNull(mixed $value): ?string
    {
        return is_scalar($value) || $value instanceof Stringable ? (string)$value : null;
    }

    public static function intOrNull(mixed $value): ?int
    {
        if (is_int($value) || $value === null) {
            return $value;
        }

        $value = filter_var(self::stringOrNull($value), FILTER_VALIDATE_INT);
        return is_int($value) ? $value : null;
    }

    public static function floatOrNull(mixed $value): ?float
    {
        if (is_float($value) || $value === null) {
            return $value;
        }

        $value = filter_var(self::stringOrNull($value), FILTER_VALIDATE_FLOAT);
        return is_float($value) ? $value : null;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public static function instanceOf(mixed $value, string $class): object
    {
        return is_object($value) && $value instanceof $class
            ? $value
            : throw new TypeError('The value is unexpected object');
    }
}
