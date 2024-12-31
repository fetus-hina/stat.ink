<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\BattleKillDeathColumn;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Kills / Deaths'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    return BattleKillDeathColumn::widget([
      'assist' => $model->assist,
      'death' => $model->death,
      'kill' => $model->kill,
      'kill_or_assist' => $model->kill_or_assist,
    ]);
  },
];
