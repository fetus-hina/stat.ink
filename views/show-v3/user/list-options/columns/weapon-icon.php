<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;

return [
  '-label' => Yii::t('app', 'Weapon (Icon)'),
  'contentOptions' => ['class' => 'cell-weapon-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-weapon-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => Icon::s3Weapon($model->weapon) ?? Icon::unknown(),
];
