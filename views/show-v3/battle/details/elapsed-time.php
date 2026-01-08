<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Elapsed Time'),
  'value' => function (Battle3 $model): ?string {
    if ($model->start_at === null || $model->end_at === null) {
      return null;
    }

    $t1 = @strtotime($model->start_at);
    $t2 = @strtotime($model->end_at);
    if ($t1 === false || $t2 === false) {
      return null;
    }

    $value = $t2 - $t1;
    if ($value < 0 || $value > 900) {
      return null;
    }

    return vsprintf('%d:%02d (%s)', [
      (int)floor($value / 60),
      $value % 60,
      Yii::t('app', '{sec,plural,=1{# second} other{# seconds}}', ['sec' => $value]),
    ]);
  },
];
