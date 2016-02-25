<?php
require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');

function smarty_modifier_as_date($string, $format = 'medium')
{
    if ($string != '' && $string != '0000-00-00' && $string != '0000-00-00 00:00:00') {
        $timestamp = smarty_make_timestamp($string);
    } elseif ($default_date != '') {
        $timestamp = smarty_make_timestamp($default_date);
    } else {
        return;
    }
    return \Yii::$app->formatter->asDate($timestamp, $format);
}
