<?php
function smarty_modifier_relative_time($string)
{
    $formatter = \Yii::$app->formatter;
    return $formatter->asRelativeTime(
        strtotime($string),
        $_SERVER['REQUEST_TIME']
    );
}
