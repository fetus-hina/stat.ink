<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
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
            match ($key) {
              'golden-egg' => Icon::goldenEgg(),
              'power-egg' => Icon::powerEgg(),
            },
            Html::tag(
              'span',
              Html::encode($count === null ? '-' : Yii::$app->formatter->asInteger($count)),
              [
                'class' => 'auto-tooltip',
                'title' => match ($key) {
                  'golden-egg' => Yii::t('app-salmon2', 'Golden Eggs'),
                  'power-egg' => Yii::t('app-salmon2', 'Power Eggs'),
                },
              ],
            ),
          ]),
          ['class' => 'mr-2'],
        );
      },
      array_keys($data),
      array_values($data),
    ));
  },
];
