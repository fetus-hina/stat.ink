<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var View $this
 */

return [
  'contentOptions' => function (array $row): array {
    $appearances = (int)ArrayHelper::getValue($row, 'appearances');
    $defeated = (int)ArrayHelper::getValue($row, 'defeated');
    return [
      'class' => 'text-right',
      'data-sort-value' => $appearances < 1 ? -1.0 : ($defeated / $appearances),
    ];
  },
  'format' => ['percent', 1],
  'headerOptions' => [
    'class' => 'text-center',
    'data' => [
      'sort' => 'float',
      'sort-default' => 'desc',
    ],
  ],
  'label' => Yii::t('app-salmon3', 'Defeat %'),
  'value' => function (array $row): ?float {
    $appearances = (int)ArrayHelper::getValue($row, 'appearances');
    $defeated = (int)ArrayHelper::getValue($row, 'defeated');
    return $appearances < 1 ? null : ($defeated / $appearances);
  },
];
