<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use DateTimeInterface;
use DateTimeZone;
use app\components\helpers\DateTimeFormatter;
use yii\base\InvalidArgumentException;

final class DateTimeApiFormatter
{
    /**
     * @param DateTimeInterface|int|string|null $value
     */
    public static function toJson($value): ?array
    {
        $value = self::convert($value);
        return $value === null
            ? null
            : DateTimeFormatter::unixTimeToJsonArray($value, new DateTimeZone('Etc/UTC'));
    }

    /**
     * @param DateTimeInterface|int|string|null $value
     */
    private static function convert($value): ?int
    {
        if ($value === null || \is_int($value)) {
            return $value;
        } elseif ($value instanceof DateTimeInterface) {
            return $value->getTimestamp();
        } elseif (\is_string($value)) {
            $t = @\strtotime($value);
            if (\is_int($t)) {
                return $t;
            }
        }

        throw new InvalidArgumentException();
    }
}
