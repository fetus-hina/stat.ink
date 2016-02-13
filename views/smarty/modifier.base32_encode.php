<?php
use Base32\Base32;

function smarty_modifier_base32_encode($string, $padding = true)
{
    $b32 = Base32::encode($string);
    return $padding ? $b32 : rtrim($b32, '=');
}
