<?php

declare(strict_types=1);

use app\assets\SalmonEggAsset;
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
    
    $asset = SalmonEggAsset::register($v);
    return implode('', array_map(
      function (string $key, ?int $count) use ($asset): string {
        return Html::tag(
          'span',
          vsprintf('%s %s', [
            Html::img(
              Yii::$app->assetManager->getAssetUrl($asset, sprintf('%s.png', $key)),
              ['class' => 'basic-icon'],
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
