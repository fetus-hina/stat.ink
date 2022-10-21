<?php

declare(strict_types=1);

return [
  'attribute' => 'end_at',
  'contentOptions' => ['class' => 'cell-datetime'],
  'format' => ['htmlDatetime', 'short'],
  'headerOptions' => ['class' => 'cell-datetime'],
  'label' => Yii::t('app', 'Date Time'),
];
