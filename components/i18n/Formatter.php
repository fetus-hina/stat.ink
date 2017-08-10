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
}
