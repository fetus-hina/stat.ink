<?php

declare(strict_types=1);

use app\components\widgets\v3\ChallengeProgress;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Rank-up Battle'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if (
      $model->is_rank_up_battle !== true || 
      $model->challenge_win === null ||
      $model->challenge_lose === null
    ) {
      return null;
    }

    return ChallengeProgress::widget([
      'win' => $model->challenge_win,
      'lose' => $model->challenge_lose,
      'isRankUpBattle' => true,
    ]);
  },
];
