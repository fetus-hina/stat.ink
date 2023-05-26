<?php

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

return [
  'label' => Yii::t('app-salmon3', 'Eggs'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->golden_eggs == null || $model->power_eggs === null) {
      return null;
    }

    $v = Yii::$app->view;
    if (!$v instanceof View) {
      return null;
    }

    $data = [
      'golden-egg' => $model->golden_eggs,
      'power-egg' => $model->power_eggs,
    ];
    
    return implode('', array_map(
      function (string $key, ?int $count): string {
        return Html::tag(
          'span',
          vsprintf('%s %s', [
            Html::tag(
              'span',
              '●',
              ['class' => 'text-' . $key],
            ),
            Html::encode($count === null ? '-' : Yii::$app->formatter->asInteger($count)),
          ]),
          [
            'class' => 'auto-tooltip mr-2',
            'title' => match ($key) {
              'golden-egg' => Yii::t('app-salmon2', 'Golden Eggs'),
              'power-egg' => Yii::t('app-salmon2', 'Power Eggs'),
            },
          ],
        );
      },
      array_keys($data),
      array_values($data),
    ));
  },
];
