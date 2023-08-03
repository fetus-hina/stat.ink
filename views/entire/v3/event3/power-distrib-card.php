<?php

declare(strict_types=1);

use app\models\Event3StatsPower;
use app\models\Event3StatsPowerHistogram;
use app\models\Event3StatsPowerPeriodHistogram;
use app\models\EventPeriod3;
use app\models\EventSchedule3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Event3StatsPower $abstract
 * @var Event3StatsPowerHistogram[] $histogram
 * @var Event3StatsPowerPeriodHistogram[] $periodHistogram
 * @var EventSchedule3 $schedule
 * @var View $this
 * @var array<int, Event3StatsPowerPeriod> $periodAbstracts
 */

if (!$abstract) {
  return;
}

$periods = ArrayHelper::sort(
  $schedule->eventPeriod3s,
  fn (EventPeriod3 $a, EventPeriod3 $b): int => strcmp($a->start_at, $b->start_at),
);

?>
<div class="panel panel-default">
  <?= $this->render('power-distrib-card/heading') . "\n" ?>
  <?= Html::tag(
    'div',
    implode('', [
      $this->render('power-distrib-card/alert-aggregate-target'),
      $this->render('power-distrib-card/table', compact(
        'abstract',
        'periodAbstracts',
        'periods',
        'schedule',
      )),
      $this->render('power-distrib-card/histograms', compact(
        'abstract',
        'histogram',
        'periodHistogram',
        'periods',
      )),
    ]),
    ['class' => 'panel-body pb-0'],
  ) . "\n" ?>
</div>
