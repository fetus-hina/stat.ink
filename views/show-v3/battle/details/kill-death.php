<?php

declare(strict_types=1);

use app\components\widgets\BattleKillDeathColumn;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Kills / Deaths'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    return BattleKillDeathColumn::widget([
      'assist' => $model->assist,
      'death' => $model->death,
      'kill' => $model->kill,
      'kill_or_assist' => $model->kill_or_assist,
    ]);
  },
];
