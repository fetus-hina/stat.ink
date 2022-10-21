<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Battle3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app', 'Special (Icon)'),
  'contentOptions' => ['class' => 'cell-special-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-special-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => $model->weapon && $model->weapon->special
    ? SpecialIcon::widget(['model' => $model->weapon->special])
    : '?',
];
