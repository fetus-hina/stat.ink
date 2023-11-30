<?php

declare(strict_types=1);

use app\components\helpers\StandardError;
use app\models\StatWeapon3Usage;
use app\models\StatWeapon3UsagePerVersion;
use app\models\StatWeapon3XUsage;
use yii\bootstrap\Progress;

/**
 * @var $maxWinRate float
 */

return [
  'contentOptions' => fn (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model): array => [
    'data-sort-value' => (string)(100.0 * $model->wins / $model->battles),
  ],
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'float',
    'data-sort-default' => 'desc',
  ],
  'label' => Yii::t('app', 'Win %'),
  'value' => function (StatWeapon3Usage|StatWeapon3UsagePerVersion|StatWeapon3XUsage $model) use ($maxWinRate): string {
    $fmt = Yii::$app->formatter;
    $stderr = StandardError::winpct($model->wins, $model->battles);

    return Progress::widget([
      'label' => implode(' ', [
        $fmt->asPercent($model->wins / $model->battles, 2),
        $stderr ? $stderr['significant'] : '',
      ]),
      'options' => [
        'class' => 'auto-tooltip',
        'style' => 'min-width:100px',
        'title' => $stderr
          ? Yii::t('app', '{from} - {to}', [
            'from' => $fmt->asPercent($stderr['min99ci'], 2),
            'to' => $fmt->asPercent($stderr['max99ci'], 2),
          ])
          : '',
      ],
      'percent' => 100 * (($model->wins / $model->battles) / $maxWinRate),
    ]);
  },
];
