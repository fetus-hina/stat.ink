<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\openapi;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Yii;
use yii\base\Component;

class DateTime extends Component
{
    use Util;

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Date and time'),
            'properties' => [
                'time' => [
                    'type' => 'integer',
                    'format' => 'int64',
                    'description' => Yii::t('app-apidoc1', 'Date and time expressed in Unix Time'),
                ],
                'iso8601' => [
                    'type' => 'string',
                    'format' => 'date-time',
                    'description' => Yii::t(
                        'app-apidoc1',
                        'Date and time expressed in ISO-8601 format',
                    ),
                ],
            ],
        ];
    }

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example($value): array
    {
        if (!$value instanceof DateTimeInterface) {
            if (filter_var($value, FILTER_VALIDATE_INT)) {
                $value = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
                    ->setTimestamp((int)$value);
            } else {
                $value = (new DateTimeImmutable($value))
                    ->setTimezone(new DateTimeZone(Yii::$app->timeZone));
            }
        }

        return [
            'time' => $value->getTimestamp(),
            'iso8601' => $value->format(\DateTime::ATOM),
        ];
    }
}
