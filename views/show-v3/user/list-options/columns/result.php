<?php

declare(strict_types=1);

use app\components\widgets\v3\Result;
use app\models\Battle3;

return [
  'contentOptions' => ['class' => 'cell-result'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-result'],
  'label' => Yii::t('app', 'Result'),
  'value' => fn (Battle3 $model): string => Result::widget([
    'isKnockout' => $model->is_knockout,
    'result' => $model->result,
    'rule' => $model->rule,
    'separator' => mb_chr(0xa0, 'UTF-8'),
  ]) ?: '?',
];
