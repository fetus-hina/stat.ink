<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;

$t = function (?string $start, ?string $end): ?int {
  if ($start === null || $end === null) {
    return null;
  }
  
  $tS = @strtotime($start);
  $tE = @strtotime($end);
  return (is_int($tS) && is_int($tE) && $tE > $tS) ? $tE - $tS : null;
};

return [
  '-label' => Yii::t('app', 'Elapsed Time'),
  'contentOptions' => ['class' => 'cell-elapsed text-right'],
  'headerOptions' => ['class' => 'cell-elapsed'],
  'label' => Yii::t('app', 'Elapsed'),
  'value' => function (Battle3 $model) use ($t): ?string {
    $value = $t($model->start_at, $model->end_at);
    return $value
      ? sprintf('%d:%02d', (int)floor($value / 60), $value % 60)
      : null;
  },
];
