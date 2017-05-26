<?php
require_once(SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php');

function smarty_modifier_as_isotime($string)
{
    if ($string != '' && $string != '0000-00-00' && $string != '0000-00-00 00:00:00') {
        $timestamp = smarty_make_timestamp($string);
    } else {
        return;
    }
    return gmdate('Y-m-d\TH:i:s\Z', $timestamp);
}
