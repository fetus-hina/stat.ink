<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Salmon3;

return [
  'label' => Yii::t('app-salmon2', 'Job Ended'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if ($model->end_at === null) {
      return null;
    }

    return TimestampColumnWidget::widget([
      'value' => $model->end_at,
      'showRelative' => true,
    ]);
  },
];
