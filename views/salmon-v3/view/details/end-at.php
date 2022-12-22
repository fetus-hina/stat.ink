<?php

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Salmon3;

return [
  'label' => Yii::t('app-salmon2', 'Job Ended'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->end_at === null) {
      return null;
    }

    return TimestampColumnWidget::widget([
      'value' => $model->end_at,
      'showRelative' => true,
    ]);
  },
];
