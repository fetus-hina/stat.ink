<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon2', 'Power Eggs'),
  'attribute' => 'salmonPlayer3s.0.power_eggs',
  'contentOptions' => ['class' => 'cell-power nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-power text-center'],
  'label' => Icon::powerEgg(),
];
