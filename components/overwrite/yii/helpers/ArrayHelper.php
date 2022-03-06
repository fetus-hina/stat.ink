<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace yii\helpers;

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
}
