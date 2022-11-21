<?php

declare(strict_types=1);

use app\models\Salmon3;

return [
  'label' => Yii::t('app', 'Game Version'),
  'value' => function (Salmon3 $model): string {
    if (!$model->version) {
      return Yii::t('app', 'Unknown');
    }

    return $model->version->name;
  },
];
