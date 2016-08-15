<?php
function smarty_modifiercompiler_percent($params)
{
    return sprintf(
        '\Yii::$app->formatter->asPercent(floatval(%s), %d)',
        $params[0],
        $params[1] ?? 1
    );
}
