<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Event3StatsWeapon;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Weapon'),
  'headerOptions' => [
    'data-sort' => 'string',
    'data-sort-default' => 'asc',
  ],
  'contentOptions' => fn (Event3StatsWeapon $model): array => [
    'class' => 'auto-tooltip',
    'title' => vsprintf('%s %s / %s %s', [
      Yii::t('app', 'Sub:'),
      Yii::t('app-subweapon3', $model->weapon->subweapon?->name ?? '?'),
      Yii::t('app', 'Special:'),
      Yii::t('app-special3', $model->weapon->special?->name ?? '?'),
    ]),
    'data' => [
      'sort-value' => Yii::t('app-weapon3', $model->weapon->name),
    ],
  ],
  'format' => 'raw',
  'value' => fn (Event3StatsWeapon $model): string => implode(' ', [
    Icon::s3Weapon($model->weapon),
    Html::encode(Yii::t('app-weapon3', $model->weapon->name)),
  ]),
];
