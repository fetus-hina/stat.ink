<?php

declare(strict_types=1);

use app\components\grid\CalcKillRatioColumn;

return [
  '-label' => Yii::t('app', 'Kill Ratio'),
  'class' => CalcKillRatioColumn::class,
  'headerOptions' => ['class' => 'cell-kill-ratio'],
  'killRate' => false,
];
