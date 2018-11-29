<?php
declare(strict_types=1);

use statink\yii2\bukiicons\Bukiicons;

function smarty_modifier_bukiicon(string $key): string
{
    return Bukiicons::url($key);
}
