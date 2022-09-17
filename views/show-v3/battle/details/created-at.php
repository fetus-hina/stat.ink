<?php

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Data Sent'),
  'format' => 'raw',
  'value' => function (Battle3 $model): string {
    return TimestampColumnWidget::widget([
      'value' => $model->created_at,
      'showRelative' => true,
    ]);
  },
];
