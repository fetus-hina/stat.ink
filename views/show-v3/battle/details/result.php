<?php

declare(strict_types=1);

use app\components\widgets\v3\Result;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Result'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    return Result::widget([
      'isKnockout' => $model->is_knockout,
      'result' => $model->result,
      'rule' => $model->rule,
    ]);
  },
];
