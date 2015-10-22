<?php
function smarty_modifier_relative_time($string, $mode = 'long')
{
    $formatter = \Yii::$app->formatter;
    $now = @$_SERVER['REQUEST_TIME'] ?: time();

    switch ($mode) {
        case 'short':
            $time = $formatter->asTimestamp($string);
            $diff = $now - $time;
            if ($diff >= 31536000) {
                return \Yii::t('app-reltime', '{delta} yr', ['delta' => (int)floor($diff / 31536000)]);
            } elseif ($diff >= 2592000) {
                return \Yii::t('app-reltime', '{delta} mo', ['delta' => (int)floor($diff / 2592000)]);
            } elseif ($diff >= 86400) {
                return \Yii::t('app-reltime', '{delta} d', ['delta' => (int)floor($diff / 86400)]);
            } elseif ($diff >= 3600) {
                return \Yii::t('app-reltime', '{delta} h', ['delta' => (int)floor($diff / 3600)]);
            } elseif ($diff >= 60) {
                return \Yii::t('app-reltime', '{delta} m', ['delta' => (int)floor($diff / 60)]);
            } elseif ($diff >= 15) {
                return \Yii::t('app-reltime', '{delta} s', ['delta' => (int)$diff]);
            } else {
                return \Yii::t('app-reltime', 'now');
            }

        case 'long':
        default:
            return $formatter->asRelativeTime($string, $now);
    }
}
