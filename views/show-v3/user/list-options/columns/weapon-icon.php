<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\WeaponIcon;
use app\models\Battle3;

return [
  '-label' =>  Yii::t('app', 'Weapon (Icon)'),
  'contentOptions' => ['class' => 'cell-main-weapon-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-main-weapon-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => $model->weapon
    ? WeaponIcon::widget(['model' => $model->weapon])
    : '?',
];
