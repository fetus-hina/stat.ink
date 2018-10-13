<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\i18n;

use DateTime;
use yii\helpers\Html;

class Formatter extends \yii\i18n\Formatter
{
    public function asHtmlDatetime($value, $format = null)
    {
        if ($value === null) {
            return $this->asDatetime($value, $format);
        }

        $timestamp = (int)$this->asTimestamp($value);
        return Html::tag(
            'time',
            Html::encode($this->asDatetime($timestamp, $format)),
            ['datetime' => gmdate(Datetime::ATOM, $timestamp)]
        );
    }

    public function asMetricPrefixed($value, int $decimal = 1): ?string
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $prefixes = [
            'Y' => pow(10, 24),
            'Z' => pow(10, 21),
            'E' => pow(10, 18),
            'P' => pow(10, 15),
            'T' => pow(10, 12),
            'G' => pow(10, 9),
            'M' => pow(10, 6),
            'k' => pow(10, 3),
        ];
        foreach ($prefixes as $prefix => $weight) {
            if ($value >= $weight) {
                return sprintf(
                    '%s%s',
                    $this->asDecimal($value / $weight, $decimal),
                    $prefix
                );
            }
        }

        return is_int($value)
            ? $this->asInteger($value)
            : $this->asDecimal($value, $decimal);
    }
}
