<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, array<int, array{battles: int, wins: int}>> $data
 */

$totalBattles = 0;
foreach ($data as $tmp1) {
  foreach ($tmp1 as $tmp2) {
    $totalBattles += $tmp2['battles'];
  }
}

echo Html::tag(
  'p',
  Html::encode(
    vsprintf('%s: %s', [
      Yii::t('app', 'Samples'),
      Yii::$app->formatter->asInteger($totalBattles),
    ]),
  ),
  ['class' => 'mb-2'],
);
