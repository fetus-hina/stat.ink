<?php

declare(strict_types=1);

return [
  'attribute' => 'end_at',
  'contentOptions' => ['class' => 'cell-datetime-timezone'],
  'format' => ['datetime', 'zzz'],
  'headerOptions' => ['class' => 'cell-datetime-timezone'],
  'label' => Yii::t('app', 'TZ'),
];
