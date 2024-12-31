<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\Color;
use app\models\StatInkColor3;
use yii\helpers\Html;

return [
  'contentOptions' => fn (StatInkColor3 $model): array => [
    'class' => 'text-center small',
    'style' => [
      'background-color' => "#{$model->color1}",
      'color' => '#fff',
      'vertical-align' => 'middle',
    ],
  ],
  'format' => 'raw',
  'headerOptions' => [
    'class' => 'text-center',
    'width' => '15%',
  ],
  'label' => Yii::t('app', 'Color 1'),
  'value' => function (StatInkColor3 $model): string {
    $f = Yii::$app->formatter;
    [$luminance, ] = Color::getYUVFromRGB(
      hexdec(substr($model->color1, 0, 2)),
      hexdec(substr($model->color1, 2, 2)),
      hexdec(substr($model->color1, 4, 2)),
    );

    return Html::tag(
      'span',
      Html::encode($f->asDecimal($luminance, 3)),
      [
        'class' => 'auto-tooltip',
        'title' => vsprintf('%s: %s', [
          Yii::t('app', 'Luminance'),
          $f->asDecimal($luminance, 3),
        ]),
      ],
    );
  },
];
