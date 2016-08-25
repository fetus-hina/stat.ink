<?php
use Yii;
use app\models\Rank;

function smarty_modifier_int2rank($int)
{
    $rank = Rank::find()
        ->andWhere(['<=', '{{rank}}.[[int_base]]', $int])
        ->orderBy('{{rank}}.[[int_base]] DESC')
        ->limit(1)
        ->asArray()
        ->one();
    if (!$rank) {
        return;
    }

    return sprintf(
        '%s %d',
        Yii::t('app-rank', $rank['name']),
        $int - $rank['int_base']
    );
}
