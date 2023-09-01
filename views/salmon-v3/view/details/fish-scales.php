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

    $labels = [
      'gold' => [
        Icon::goldScale(),
        Yii::t('app-salmon-scale3', 'Gold'),
      ],
      'silver' => [
        Icon::silverScale(),
        Yii::t('app-salmon-scale3', 'Silver'),
      ],
      'bronze' => [
        Icon::bronzeScale(),
        Yii::t('app-salmon-scale3', 'Bronze'),
      ],
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
              Html::tag('span', $labels[$key][0] ?? '', [
                'class' => 'auto-tooltip',
                'title' => $labels[$key][1] ?? '',
              ]),
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
