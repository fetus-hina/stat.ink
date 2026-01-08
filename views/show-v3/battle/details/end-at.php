<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Battle End'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->end_at === null) {
      return null;
    }

    return TimestampColumnWidget::widget([
      'value' => $model->end_at,
      'showRelative' => true,
    ]);
  },
];
