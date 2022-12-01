<?php

declare(strict_types=1);

use app\components\widgets\v3\XProgress;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Series Progress'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if (
      $model->lobby?->key !== 'xmatch' ||
      $model->challenge_win === null ||
      $model->challenge_lose === null
    ) {
      return null;
    }

    return XProgress::widget([
      'win' => $model->challenge_win,
      'lose' => $model->challenge_lose,
    ]);
  },
];
