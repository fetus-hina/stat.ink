<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Splatfest3StatsWeapon;

return [
  'label' => '',
  'headerOptions' => [
    'data-sort' => 'string',
    'data-sort-default' => 'asc',
  ],
  'contentOptions' => fn (Splatfest3StatsWeapon $model): array => [
    'data-sort-value' => Yii::t('app-special3', $model->weapon->special->name),
  ],
  'format' => 'raw',
  'value' => fn (Splatfest3StatsWeapon $model): string => Icon::s3special($model->weapon?->special),
];
