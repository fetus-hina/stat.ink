<?php

declare(strict_types=1);

use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon2', 'Team total Power Eggs'),
  'attribute' => 'power_eggs',
  'contentOptions' => ['class' => 'cell-power-total nobr text-right'],
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-power-total text-center'],
  'label' => Yii::t('app-salmon2', 'Pwr Eggs'),
];
