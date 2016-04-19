<?php
function smarty_modifiercompiler_number_format($params)
{
    return sprintf(
        '\Yii::$app->formatter->asDecimal(floatval(%s), %d)',
        $params[0],
        $params[1] ?? 0
    );
}
