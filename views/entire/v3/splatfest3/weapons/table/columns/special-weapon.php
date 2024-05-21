<?php

declare(strict_types=1);

use app\models\Splatfest3StatsWeapon;

return [
  'label' => Yii::t('app', 'Special'),
  'value' => fn (Splatfest3StatsWeapon $model): string => Yii::t('app-special3', $model->special->name),
];
