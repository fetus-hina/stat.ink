<?php

declare(strict_types=1);

use app\models\StatWeapon3Usage;
use yii\bootstrap\Progress;

/**
 * @var int $maxBattles
 * @var int $totalBattles
 */

return [
  'contentOptions' => fn (StatWeapon3Usage $model): array => [
    'data-sort-value' => $model->battles,
  ],
  'format' => 'raw',
  'headerOptions' => ['data-sort' => 'int'],
  'label' => '', // Yii::t('app', 'Players'),
  'value' => fn (StatWeapon3Usage $model): string => Progress::widget([
    'label' => Yii::$app->formatter->asPercent($model->battles / $totalBattles, 2),
    'options' => ['style' => 'min-width:50px'],
    'percent' => 100.0 * $model->battles / $maxBattles,
  ]),
];
