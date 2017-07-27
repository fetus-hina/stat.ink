<?php
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
}
