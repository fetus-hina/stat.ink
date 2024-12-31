<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;

return [
  'contentOptions' => ['class' => 'omit'],
  'format' => 'text',
  'label' => Yii::t('app-salmon3', 'Job Scenario'),
  'value' => function (Salmon3 $model): ?string {
    $code = $model->scenario_code;
    if (!$code || !is_string($code)) {
      return null;
    }

    $code = (string)preg_replace('/[^A-Z0-9]+/', '', strtoupper($code));
    if (!preg_match('/^[A-Z0-9]{16}/', $code)) {
      return null;
    }

    return implode('-', [
      substr($code, 0, 4),
      substr($code, 4, 4),
      substr($code, 8, 4),
      substr($code, 12, 4),
    ]);
  },
];
