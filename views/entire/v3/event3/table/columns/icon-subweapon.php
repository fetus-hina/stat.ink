<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Event3StatsWeapon;

return [
  'label' => '',
  'headerOptions' => [
    'data-sort' => 'string',
    'data-sort-default' => 'asc',
  ],
  'contentOptions' => fn (Event3StatsWeapon $model): array => [
    'data-sort-value' => Yii::t('app-subweapon3', $model->weapon?->subweapon?->name ?? ''),
  ],
  'format' => 'raw',
  'value' => fn (Event3StatsWeapon $model): string => Icon::s3Subweapon($model->weapon?->subweapon),
];
