<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'attribute' => 'map_id',
  'label' => Yii::t('app', 'Stage'),
  'value' => function (Battle3 $model): string {
    $map = $model->map;
    return $map ? Yii::t('app-map3', $map->name) : '?';
  },
];
