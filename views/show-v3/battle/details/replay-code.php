<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'contentOptions' => [
    'class' => 'omit',
    'id' => 'replay-cell',
  ],
  'label' => Yii::t('app', 'Replay Code'),
  'format' => 'replayCode3',
  'attribute' => 'replay_code',
];
