<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace yii\helpers;

use Yii;

class ArrayHelper extends BaseArrayHelper
{
    public static function toFlatten(array $array): array
    {
        $result = [];
        foreach ($array as $value) {
            if (is_array($value)) {
                $result = static::merge($result, static::toFlatten($value));
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

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
        } else {
            usort($array, $callback);
        }
        return $array;
    }
}
