<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app', 'Lobby (Icon)'),
  'contentOptions' => ['class' => 'cell-lobby-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-lobby-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => Icon::s3Lobby($model->lobby)
    ?? Icon::unknown(),
];
