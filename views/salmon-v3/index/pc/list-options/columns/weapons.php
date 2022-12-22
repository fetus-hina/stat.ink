<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Salmon3;
use app\models\SalmonPlayerWeapon3;
use yii\helpers\ArrayHelper;

return [
  'contentOptions' => ['class' => 'cell-weapon nobr'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-weapon'],
  'label' => Yii::t('app', 'Weapons'),
  'value' => function (Salmon3 $model): ?string {
    $players = $model->salmonPlayer3s;
    if (!$players) {
      return null;
    }

    if (!$player = array_shift($players)) {
      return null;
    }

    if (!$weapons = $player->salmonPlayerWeapon3s) {
      return null;
    }

    return implode(
      '',
      array_map(
        fn (SalmonPlayerWeapon3 $weapon): string => WeaponIcon::widget(['model' => $weapon->weapon]),
        ArrayHelper::sort(
          $weapons,
          fn (SalmonPlayerWeapon3 $a, SalmonPlayerWeapon3 $b): int => $a->wave <=> $b->wave,
        ),
      ),
    );
  },
];
