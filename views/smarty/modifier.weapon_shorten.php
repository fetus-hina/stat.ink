<?php
function smarty_modifier_weapon_shorten(string $weapon)
{
    return Yii::$app->weaponShortener->get($weapon);
}
