<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

return [
  'label' => Yii::t('app-salmon3', 'Scales'),
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

    $icons = [
      'gold' => Icon::goldScale(),
      'silver' => Icon::silverScale(),
      'bronze' => Icon::bronzeScale(),
    ];

    $counts = [
      'gold' => $model->gold_scale,
      'silver' => $model->silver_scale,
      'bronze' => $model->bronze_scale,
    ];
    
    return implode(
      '', // mb_chr(0x2003, 'UTF-8'), // em-space
      array_filter(
        array_map(
          fn (string $key, int $count): string => Html::tag(
            'span',
            vsprintf('%s %s', [
              $icons[$key] ?? throw new LogicException('Unknown key: ' . $key),
              Html::encode(Yii::$app->formatter->asInteger($counts[$key] ?? 0)),
            ]),
            ['class' => 'mr-2'],
          ),
          array_keys($counts),
          array_values($counts),
        ),
      ),
    );
  },
];
