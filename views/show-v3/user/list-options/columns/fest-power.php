<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'contentOptions' => ['class' => 'cell-fest-power text-right'],
  'format' => ['decimal', 1],
  'headerOptions' => ['class' => 'cell-fest-power'],
  'label' => Yii::t('app', 'Splatfest Power'),
  'value' => fn (Battle3 $model): ?float => $model->fest_power < 0.1
    ? null
    : (float)$model->fest_power,
];
