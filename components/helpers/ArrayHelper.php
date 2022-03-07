<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use yii\helpers\ArrayHelper as BaseArrayHelper;

final class ArrayHelper extends BaseArrayHelper
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
