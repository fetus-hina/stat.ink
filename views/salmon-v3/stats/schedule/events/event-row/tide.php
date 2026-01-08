<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var int|null $cleared
 * @var int|null $tideWaves
 * @var int|null $totalDelivered
 * @var int|null $totalQuota
 * @var int|null $totalWaves
 * @var int|null $waves
 */

if ($waves < 1 || $totalWaves < 1) {
  echo str_repeat(
    Html::tag('td', '', ['class' => 'text-center']),
    4,
  );
  return;
}

$fmt = Yii::$app->formatter;

echo Html::tag(
  'td',
  $fmt->asInteger((int)$waves),
  ['class' => 'text-center'],
);
echo Html::tag(
  'td',
  $fmt->asPercent(
    (float)$waves / (float)$totalWaves,
    2,
  ),
  ['class' => 'text-center'],
);
echo Html::tag(
  'td',
  $fmt->asPercent(
    (float)$cleared / (float)$waves,
    1,
  ),
  ['class' => 'text-center'],
);
echo Html::tag(
  'td',
  $fmt->asDecimal(
    (float)$totalDelivered / (float)$waves,
    1,
  ),
  ['class' => 'text-center'],
);
