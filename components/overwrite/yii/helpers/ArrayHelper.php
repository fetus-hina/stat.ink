<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace yii\helpers;

use yii\base\InvalidArgumentException;

use function asort;
use function is_array;
use function is_callable;
use function sort;
use function uasort;
use function usort;

use const SORT_FLAG_CASE;
use const SORT_LOCALE_STRING;
use const SORT_NATURAL;
use const SORT_NUMERIC;
use const SORT_REGULAR;
use const SORT_STRING;

class ArrayHelper extends BaseArrayHelper
{
    /**
     * @param array<mixed> $array
     * @return array<mixed>
     */
    public static function toFlatten(array $array): array
    {
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = static::merge(
                    $result,
                    static::toFlatten($value),
                );
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * @template T
     * @param array<T> $array
     * @param int|(callable(T, T): int)|null $callback
     * @return list<T>
     */
    public static function sort(array $array, $callback = null): array
    {
        if ($callback === null) {
            $callback = SORT_NATURAL;
        }

        if (
            $callback === SORT_REGULAR ||
            $callback === SORT_NUMERIC ||
            $callback === SORT_STRING ||
            $callback === SORT_LOCALE_STRING ||
            $callback === SORT_NATURAL ||
            $callback === (SORT_FLAG_CASE | SORT_STRING) ||
            $callback === (SORT_FLAG_CASE | SORT_NATURAL)
        ) {
            sort($array, $callback);
        } elseif (is_callable($callback)) {
            usort($array, $callback);
        } else {
            throw new InvalidArgumentException('Unsupported sort flags');
        }

        return $array;
    }

    /**
     * @template TKey of array-key
     * @template T
     * @param array<TKey, T> $array
     * @param int|(callable(T, T): int)|null $callback
     * @return array<TKey, T>
     */
    public static function asort(array $array, $callback = null): array
    {
        if ($callback === null) {
            $callback = SORT_NATURAL;
        }

        if (
            $callback === SORT_REGULAR ||
            $callback === SORT_NUMERIC ||
            $callback === SORT_STRING ||
            $callback === SORT_LOCALE_STRING ||
            $callback === SORT_NATURAL ||
            $callback === (SORT_FLAG_CASE | SORT_STRING) ||
            $callback === (SORT_FLAG_CASE | SORT_NATURAL)
        ) {
            asort($array, $callback);
        } elseif (is_callable($callback)) {
            uasort($array, $callback);
        } else {
            throw new InvalidArgumentException('Unsupported sort flags');
        }

        return $array;
    }
}
