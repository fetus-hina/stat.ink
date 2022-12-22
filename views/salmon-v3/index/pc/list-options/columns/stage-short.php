<?php

declare(strict_types=1);

use app\models\Salmon3;

return [
  '-label' => Yii::t('app', 'Stage (Short)'),
  'contentOptions' => ['class' => 'cell-map-short'],
  'headerOptions' => ['class' => 'cell-map-short'],
  'label' => Yii::t('app', 'Stage'),
  'value' => fn (Salmon3 $model): ?string => $model->stage_id
    ? Yii::t('app-map3', $model->stage->short_name)
    : ($model->big_stage_id ? Yii::t('app-map3', $model->bigStage->short_name) : null),
];
