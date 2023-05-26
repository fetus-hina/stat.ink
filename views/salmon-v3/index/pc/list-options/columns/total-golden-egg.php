<?php

declare(strict_types=1);

use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
  'attribute' => 'golden_eggs',
  'contentOptions' => ['class' => 'cell-golden-total nobr text-right'],
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-golden-total text-center'],
  'label' => Yii::t('app-salmon2', 'Golden'),
];
