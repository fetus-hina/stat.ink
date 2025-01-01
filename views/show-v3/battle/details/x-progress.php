<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\XProgress;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Series Progress'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if (
      $model->lobby?->key !== 'xmatch' ||
      $model->challenge_win === null ||
      $model->challenge_lose === null
    ) {
      return null;
    }

    return XProgress::widget([
      'win' => $model->challenge_win,
      'lose' => $model->challenge_lose,
    ]);
  },
];
