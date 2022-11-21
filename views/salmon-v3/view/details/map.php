<?php

declare(strict_types=1);

use app\models\Map3;
use app\models\Salmon3;
use app\models\SalmonMap3;

return [
  'label' => Yii::t('app', 'Stage'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    /** @var Map3|SalmonMap3|null */
    $stage = $model->is_big_run ? $model->bigStage : $model->stage;
    if (!$stage) {
      return null;
    }

    return Yii::t('app-map3', $stage->name);
  },
];
