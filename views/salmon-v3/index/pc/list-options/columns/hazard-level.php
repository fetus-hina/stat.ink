<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;

return [
  'contentOptions' => require __DIR__ . '/hazard-level/content-options.php',
  'format' => ['percent', 0],
  'headerOptions' => ['class' => 'cell-danger-rate'],
  'label' => Yii::t('app-salmon2', 'Hazard Level'),
  'value' => function (Salmon3 $model): ?float {
    $value = $model->danger_rate;
    return $value !== null ? ((int)$value) / 100 : null;
  },
];
