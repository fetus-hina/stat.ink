<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Battle3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app', 'Mode (Icon)'),
  'contentOptions' => ['class' => 'cell-rule-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-rule-icon'],
  'value' => function (Battle3 $model): ?string {
    if (
      !($rule = $model->rule) ||
      !($am = Yii::$app->assetManager)
    ) {
      return null;
    }

    return Html::img(
      $am->getAssetUrl(
        $am->getBundle(GameModeIconsAsset::class),
        sprintf('spl3/%s.png', $rule->key),
      ),
      [
        'class' => 'auto-tooltip',
        'title' => Yii::t('app-rule3', $rule->name),
        'style' => [
          'height' => '1.5em',
          'width' => 'auto',
        ],
      ],
    );
  },
];
