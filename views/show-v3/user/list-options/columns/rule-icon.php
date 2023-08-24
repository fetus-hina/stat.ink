<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app', 'Mode (Icon)'),
  'contentOptions' => ['class' => 'cell-rule-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-rule-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => Icon::s3Rule($model->rule)
    ?? Icon::unknown(),
];
