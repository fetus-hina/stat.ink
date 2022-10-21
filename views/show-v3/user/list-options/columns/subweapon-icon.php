<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\models\Battle3;

return [
  '-label' => Yii::t('app', 'Sub Weapon (Icon)'),
  'contentOptions' => ['class' => 'cell-sub-weapon-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-sub-weapon-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => $model->weapon && $model->weapon->subweapon
    ? SubweaponIcon::widget(['model' => $model->weapon->subweapon])
    : '?',
];
