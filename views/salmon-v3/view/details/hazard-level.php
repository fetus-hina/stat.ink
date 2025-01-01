<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Salmon3;
use yii\bootstrap\Progress;

return [
  'label' => Yii::t('app-salmon2', 'Hazard Level'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    $rate = $model->danger_rate;
    if ($rate === null) {
      return null;
    }

    $rate = (int)$rate;
    return Progress::widget([
      'percent' => min(100, $rate * 100 / 333),
      'label' => sprintf('%s%%', Yii::$app->formatter->asInteger($rate)),
      'barOptions' => ['class' => 'progress-bar progress-bar-danger mb-0'],
    ]);
  },
];
