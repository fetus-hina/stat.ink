<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use yii\base\Model;
use yii\helpers\ArrayHelper;

return [
  'label' => Yii::t('app', 'Players'),
  'attribute' => 'battles',
  'format' => 'integer',
  'headerOptions' => [
    'data-sort' => 'int',
    'data-sort-default' => 'desc',
    'data-sort-onload' => 'yes',
  ],
  'contentOptions' => function (Model $model): array {
    return [
      'class' => 'text-right',
      'data' => [
        'sort-value' => TypeHelper::int(ArrayHelper::getValue($model, 'battles', -1)),
      ],
    ];
  },
];
