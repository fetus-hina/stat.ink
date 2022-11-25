<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Salmon3;

return [
  '-label' => Yii::t('app', 'Special (Icon)'),
  'contentOptions' => ['class' => 'cell-special-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-special-icon'],
  'label' => '',
  'value' => function (Salmon3 $model): ?string {
    $players = $model->salmonPlayer3s;
    if (!$players) {
      return null;
    }

    if (!$player = array_shift($players)) {
      return null;
    }

    return SpecialIcon::widget([
      'model' => $player->special,
    ]);
  },
];
