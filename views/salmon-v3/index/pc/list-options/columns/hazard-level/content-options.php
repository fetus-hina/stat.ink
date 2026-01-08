<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\SalmonWorkListAsset;
use app\models\Salmon3;
use yii\grid\Column;

SalmonWorkListAsset::register(Yii::$app->view);

return fn (Salmon3 $model, $key, $index, Column $column): array => [
  'class' => array_filter([
    'cell-danger-rate',
    ($model->danger_rate === null ? null : 'danger-rate-bg'),
    'text-center',
  ]),
  'data' => [
    'danger-rate' => (string)$model->danger_rate,
    'max-danger-rate' => '333',
  ],
];
