<?php
function smarty_modifiercompiler_floor($params)
{
    return sprintf(
        '(floor(floatval(%1$s) * %2$d) / %2$d)',
        $params[0],
        pow(10, (int)($params[1] ?? 0))
    );
}
