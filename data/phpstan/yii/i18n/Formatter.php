<?php

namespace yii\i18n;

use DateInterval;
use DateTime;
use DateTimeInterface;

class Formatter
{
    /**
     * @param int|string|DateTime|DateTimeInterface|null $value
     * @param string|null $format the format used to convert the value into a date string.
     * @return string
     */
    public function asDate($value, $format = null)
    {
    }

    /**
     * @param int|string|DateTime|DateTimeInterface|null $value
     * @param string|null $format
     * @return string
     */
    public function asTime($value, $format = null)
    {
    }

    /**
     * @param int|string|DateTime|DateTimeInterface|null $value
     * @param string|null $format
     * @return string
     */
    public function asDatetime($value, $format = null)
    {
    }

    /**
     * @param int|string|DateTime|DateTimeInterface|DateInterval|null $value
     * @param int|string|DateTime|DateTimeInterface|null $referenceTime
     * @return string
     */
    public function asRelativeTime($value, $referenceTime = null)
    {
    }
}
