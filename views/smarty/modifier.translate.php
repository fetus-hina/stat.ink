<?php
function smarty_modifier_translate($string, $category, $params = [])
{
    return \Yii::t($category, $string, $params);
}
