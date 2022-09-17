<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Game Version'),
  'value' => function (Battle3 $model): string {
    if (!$model->version) {
      return Yii::t('app', 'Unknown');
    }

    return $model->version->name;
  },
];
