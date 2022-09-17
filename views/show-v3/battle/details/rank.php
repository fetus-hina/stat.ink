<?php

declare(strict_types=1);

use app\components\widgets\v3\Rank;
use app\models\Battle3;
use app\models\Rank3;
use yii\bootstrap\Html;

$renderRank = function (?Rank3 $rank, ?int $splusNum, ?int $pts): string {
  $html = Rank::widget([
    'model' => $rank,
    'splus' => $splusNum,
    'pts' => $pts,
  ]);
  return $html ?: Html::encode('?');
};

return [
  'label' => Yii::t('app', 'Rank'),
  'format' => 'raw',
  'value' => function (Battle3 $model) use ($renderRank): ?string {
    $rankBefore = $model->rankBefore;
    $rankAfter = $model->rankAfter;
    if (!$rankBefore && !$rankAfter) {
      return null;
    }

    return implode(' ', [
      $renderRank($rankBefore, $model->rank_before_s_plus, $model->rank_before_exp),
      Html::encode('→'),
      $renderRank($rankAfter, $model->rank_after_s_plus, $model->rank_after_exp),
    ]);
  },
];
