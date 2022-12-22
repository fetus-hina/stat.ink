<?php

declare(strict_types=1);

use app\components\widgets\v3\SalmonJobPoint;
use app\models\Salmon3;
use yii\web\View;

return [
  'label' => Yii::t('app-salmon3', 'Job Points'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (
      $model->is_private ||
      $model->job_point === null ||
      $model->job_score === null ||
      $model->job_rate === null ||
      $model->job_bonus === null
    ) {
      return null;
    }

    return SalmonJobPoint::widget([
      'jobPoint' => (int)$model->job_point,
      'jobScore' => (int)$model->job_score,
      'jobRate' => (float)$model->job_rate,
      'jobBonus' => (int)$model->job_bonus,
    ]);
  },
];
