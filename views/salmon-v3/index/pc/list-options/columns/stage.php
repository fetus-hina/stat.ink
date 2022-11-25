<?php

declare(strict_types=1);

use app\models\Salmon3;

return [
  'contentOptions' => ['class' => 'cell-map'],
  'headerOptions' => ['class' => 'cell-map'],
  'label' => Yii::t('app', 'Stage'),
  'value' => fn (Salmon3 $model): ?string => $model->stage_id
    ? Yii::t('app-map3', $model->stage->name)
    : ($model->big_stage_id ? Yii::t('app-map3', $model->bigStage->name) : null),
];
