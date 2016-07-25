<?php
require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');

function smarty_modifier_as_datetime($string, $dateFormat = 'medium', $timeFormat = 'long')
{
    if ($string != '' && $string != '0000-00-00' && $string != '0000-00-00 00:00:00') {
        $timestamp = smarty_make_timestamp($string);
    } else {
        return;
    }
    return sprintf(
        '%s %s',
        \Yii::$app->formatter->asDate($timestamp, $dateFormat),
        \Yii::$app->formatter->asTime($timestamp, $timeFormat)
    );
}
