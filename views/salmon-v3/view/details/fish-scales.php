<?php

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

return [
  'label' => Yii::t('app-salmon3', 'Fish Scales'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (
      $model->is_private ||
      $model->gold_scale === null ||
      $model->silver_scale === null ||
      $model->bronze_scale === null ||
      $model->gold_scale + $model->silver_scale + $model->bronze_scale < 1
    ) {
      return null;
    }

    $labels = [
      'gold' => Yii::t('app-salmon-scale3', 'Gold'),
      'silver' => Yii::t('app-salmon-scale3', 'Silver'),
      'bronze' => Yii::t('app-salmon-scale3', 'Bronze'),
    ];

    $counts = [
      'gold' => $model->gold_scale,
      'silver' => $model->silver_scale,
      'bronze' => $model->bronze_scale,
    ];
    
    return implode(
      ' / ',
      array_filter(
        array_map(
          fn (string $key): ?string => ($counts[$key] ?? 0) < 1
            ? null
            : Html::tag(
              'span',
              vsprintf('%s: %s', [
                Html::encode($labels[$key] ?? ''),
                Html::encode(Yii::$app->formatter->asInteger($counts[$key] ?? 0)),
              ]),
            ),
          array_keys($counts),
        ),
      ),
    );
  },
];
