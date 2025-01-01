<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\TimestampColumnWidget;
use app\models\Salmon3;

return [
  'label' => Yii::t('app', 'Data Sent'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): string {
    return TimestampColumnWidget::widget([
      'value' => $model->created_at,
      'showRelative' => true,
    ]);
  },
];
