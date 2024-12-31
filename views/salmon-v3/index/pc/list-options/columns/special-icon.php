<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Salmon3;

return [
  'contentOptions' => ['class' => 'cell-special-icon'],
  'headerOptions' => ['class' => 'cell-special-icon'],
  '-label' => Yii::t('app', 'Special (Icon)'),
  'label' => '',
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    $players = $model->salmonPlayer3s;
    if (!$players) {
      return null;
    }

    if (!$player = array_shift($players)) {
      return null;
    }

    return Icon::s3Special($player->special)
      ?? Icon::unknown();
  },
];
