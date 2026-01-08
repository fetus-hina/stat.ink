<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
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
  'attribute' => 'appearances',
  'contentOptions' => fn (array $row): array => [
    'class' => 'text-right',
    'data-sort-value' => (int)ArrayHelper::getValue($row, 'appearances'),
  ],
  'format' => 'integer',
  'headerOptions' => [
    'class' => 'text-center',
    'data' => [
      'sort' => 'int',
      'sort-default' => 'desc',
    ],
  ],
  'label' => Yii::t('app-salmon3', 'Appearances'),
];
