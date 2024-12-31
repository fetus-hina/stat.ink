<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\EventTrait;
use app\components\helpers\TypeHelper;
use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type EventStats from EventTrait
 *
 * @var EventStats $eventStats
 * @var SalmonEvent3|null $event
 * @var User $user
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 */

$tideStats = ArrayHelper::getValue($eventStats, $event?->id ?? 0);
if (!$tideStats) {
  return;
}

$totalWaves = 0;
$totalWavesPerTide = [];
foreach ($eventStats as $row1) {
  foreach ($row1 as $id2 => $row2) {
    $totalWaves += $row2['waves'];
    $totalWavesPerTide[$id2] = ($totalWavesPerTide[$id2] ?? 0) + $row2['waves'];
  }
}

?>
<tr>
  <?= Html::tag(
    'th',
    Html::encode(
      Yii::t('app-salmon-event3', $event?->name ?? '(Normal)'),
    ),
    ['class' => $event ? 'text-left' : 'text-center'],
  ) . "\n" ?>
<?php foreach ($tides as $tide) { ?>
<?php $row = ArrayHelper::getValue($tideStats, $tide->id); ?>
  <?= $this->render('event-row/tide', [
    'cleared' => TypeHelper::intOrNull(ArrayHelper::getValue($row, 'cleared')),
    'tideWaves' => $totalWavesPerTide[$tide->id] ?? 0,
    'totalDelivered' => TypeHelper::intOrNull(ArrayHelper::getValue($row, 'total_delivered')),
    'totalQuota' => TypeHelper::intOrNull(ArrayHelper::getValue($row, 'total_quota')),
    'totalWaves' => $totalWaves,
    'waves' => TypeHelper::intOrNull(ArrayHelper::getValue($row, 'waves')),
  ]) . "\n" ?>
<?php } ?>
</tr>
