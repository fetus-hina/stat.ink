<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
