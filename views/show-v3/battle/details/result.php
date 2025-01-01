<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\Result;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Result'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    return Result::widget([
      'isKnockout' => $model->is_knockout,
      'result' => $model->result,
      'rule' => $model->rule,
    ]);
  },
];
