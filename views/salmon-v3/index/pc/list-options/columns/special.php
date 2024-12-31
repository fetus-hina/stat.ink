<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;

return [
  'contentOptions' => ['class' => 'cell-special'],
  'headerOptions' => ['class' => 'cell-special'],
  'label' => Yii::t('app', 'Special'),
  'value' => function (Salmon3 $model): ?string {
    $players = $model->salmonPlayer3s;
    if (!$players) {
      return null;
    }

    if (!$player = array_shift($players)) {
      return null;
    }

    if (!$special = $player->special) {
      return null;
    }

    return Yii::t('app-special3', $special->name);
  },
];
