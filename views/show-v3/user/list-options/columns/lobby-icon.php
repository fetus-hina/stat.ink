<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Battle3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app', 'Lobby (Icon)'),
  'contentOptions' => ['class' => 'cell-lobby-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-lobby-icon'],
  'value' => function (Battle3 $model): ?string {
    if (
      !($lobby = $model->lobby) ||
      !($am = Yii::$app->assetManager)
    ) {
      return null;
    }

    return Html::img(
      $am->getAssetUrl(
        $am->getBundle(GameModeIconsAsset::class),
        sprintf('spl3/%s.png', $lobby->key),
      ),
      [
        'class' => 'auto-tooltip',
        'title' => Yii::t('app-lobby3', $lobby->name),
        'style' => [
          'height' => '1.5em',
          'width' => 'auto',
        ],
      ],
    );
  },
];
