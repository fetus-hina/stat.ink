<?php

declare(strict_types=1);

use app\assets\FishScaleAsset;
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
      $model->bronze_scale === null
    ) {
      return null;
    }

    $v = Yii::$app->view;
    if (!$v instanceof View) {
      return null;
    }

    $data = [
      'gold' => $model->gold_scale,
      'silver' => $model->silver_scale,
      'bronze' => $model->bronze_scale,
    ];
    
    $asset = FishScaleAsset::register($v);
    return implode('', array_map(
      function (string $key, ?int $count) use ($asset): string {
        return Html::tag(
          'span',
          vsprintf('%s %s', [
            Html::img(
              Yii::$app->assetManager->getAssetUrl($asset, sprintf('%s.png', $key)),
              [
                'style' => [
                  'height' => '1.5em',
                  'vertical-align' => 'middle',
                  'width' => 'auto',
                ],
              ],
            ),
            Html::encode($count === null ? '-' : Yii::$app->formatter->asInteger($count)),
          ]),
          ['class' => 'mr-2'],
        );
      },
      array_keys($data),
      array_values($data),
    ));
  },
];
