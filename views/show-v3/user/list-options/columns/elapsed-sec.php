<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;

return [
  '-label' => Yii::t('app', 'Elapsed Time (seconds)'),
  'contentOptions' => ['class' => 'cell-elapsed-sec text-right'],
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-elapsed-sec'],
  'label' => Yii::t('app', 'Elapsed'),
  'value' => function (Battle3 $model): ?int {
    if ($model->start_at === null || $model->end_at === null) {
      return null;
    }
    
    $tS = @strtotime($model->start_at);
    $tE = @strtotime($model->end_at);
    return (is_int($tS) && is_int($tE) && $tE > $tS) ? $tE - $tS : null;
  },
];
