<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\i18n;

use DateTime;
use Yii;
use app\components\widgets\TimestampColumnWidget;
use yii\helpers\Html;

use function gmdate;
use function http_build_query;
use function implode;
use function is_int;
use function is_string;
use function pow;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_starts_with;
use function strtoupper;
use function substr;
use function vsprintf;

class Formatter extends \yii\i18n\Formatter
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
            ['datetime' => gmdate(Datetime::ATOM, $timestamp)],
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
                'datetime' => gmdate(Datetime::ATOM, $timestamp),
                'title' => $this->asDatetime($timestamp, 'medium'),
                'class' => 'auto-tooltip',
            ],
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
                    $prefix,
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
        bool $escape = true,
    ): string {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $text = Yii::t($category, (string)$value, $options);
        return $escape ? $this->asText($text) : $text;
    }

    public function asReplayCode3($value, bool $link = true): string
    {
        if (!is_string($value)) {
            return $this->nullDisplay;
        }

        $value = preg_replace('/[^0-9A-Z]+/', '', strtoupper($value));
        if (!preg_match('/^[0-9A-Z]{16}$/', $value)) {
            return $this->nullDisplay;
        }

        $value = implode('-', [
            substr($value, 0, 4),
            substr($value, 4, 4),
            substr($value, 8, 4),
            substr($value, 12, 4),
        ]);

        if (!$link || !str_starts_with($value, 'R')) {
            return $this->asText($value);
        }

        $url = vsprintf('https://s.nintendo.com/av5ja-lp1/znca/game/4834290508791808?%s', [
            http_build_query(
                [
                    'p' => vsprintf('/replay?%s', [
                        http_build_query(['code' => $value]),
                    ]),
                ],
            ),
        ]);

        return Html::a(
            Html::encode($value),
            $url,
            [
                'target' => '_blank',
                'rel' => 'noopener noreferrer',
            ],
        );
    }
}
