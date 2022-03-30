<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\i18n;

use DateTimeInterface;
use Yii;
use app\components\helpers\Html;
use app\components\widgets\TimestampColumnWidget;
use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter
{
    public function asHtmlDatetime($value, $format = null)
    {
        return $this->asHtmlDatetimeEx($value, $format, $format);
    }

    public function asHtmlDatetimeEx($value, $formatD = null, $formatT = null)
    {
        if ($value === null) {
            return $this->asDatetime($value, $formatD);
        }

        $timestamp = (int)$this->asTimestamp($value);
        return Html::tag(
            'time',
            Html::encode(implode(' ', [
                $this->asDate($timestamp, $formatD),
                $this->asTime($timestamp, $formatT),
            ])),
            ['datetime' => gmdate(DateTimeInterface::ATOM, $timestamp)]
        );
    }

    public function asHtmlRelative($value): string
    {
        if ($value === null) {
            return $this->asRelativeTime($value);
        }

        $timestamp = (int)$this->asTimestamp($value);
        return Html::tag(
            'time',
            Html::encode($this->asRelativeTime($timestamp)),
            [
                'datetime' => gmdate(DateTimeInterface::ATOM, $timestamp),
                'title' => $this->asDatetime($timestamp, 'medium'),
                'class' => 'auto-tooltip',
            ]
        );
    }

    public function asTimestampColumn($value, bool $withReltime = true): ?string
    {
        return TimestampColumnWidget::widget([
            'value' => $value,
            'showRelative' => $withReltime,
            'formatter' => $this,
        ]);
    }

    public function asMetricPrefixed($value, int $decimal = 0): ?string
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

    public function asTranslated(
        $value,
        string $category = 'app',
        array $options = [],
        bool $escape = true
    ): string {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $text = Yii::t($category, (string)$value, $options);
        return $escape ? $this->asText($text) : $text;
    }
}
