<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Event3;
use app\models\EventPeriod3;
use app\models\EventSchedule3;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var ActiveDataProvider $specialProvider
 * @var ActiveDataProvider $weaponsProvider
 * @var Event3 $event
 * @var Event3[] $events
 * @var EventSchedule3 $schedule
 * @var EventSchedule3[] $schedules
 * @var View $this
 */

$title = vsprintf('%s - %s', [
  Yii::t('app', 'Weapons'),
  Yii::t('app-lobby3', 'Challenge'),
]);

OgpHelper::default($this, title: $title);

$fmt = Yii::$app->formatter;

$samples = (int)$weaponsProvider->query->sum('battles');
$periods = ArrayHelper::sort(
  $schedule->eventPeriod3s,
  fn (EventPeriod3 $a, EventPeriod3 $b): int => strcmp($a->start_at, $b->start_at),
);

?>
<div class="container">
  <?= Html::tag('h1', Html::encode($title)) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <nav class="mb-3">
    <?= $this->render('event3/lobby-tabs') . "\n" ?>
  </nav>
  <nav class="mb-3">
    <?= $this->render('event3/events', compact('event', 'events')) . "\n" ?>
    <?= $this->render('event3/schedules', compact('event', 'schedule', 'schedules')) . "\n" ?>
  </nav>

  <div class="mb-3">
    <h2 class="mb-1">
      <?= Html::encode(Yii::t('db/event3', $event->name)) . "\n" ?>
    </h2>
    <p class="mb-1 text-muted">
      <?= Html::encode(Yii::t('db/event3/description', $event->desc)) . "\n" ?>
    </p>
    <ul class="mb-1">
      <li>
        <?= Html::encode(Yii::t('app-rule3', $schedule->rule->name ?? '?')) . "\n" ?>
      </li>
<?php foreach ($periods as $period) { ?>
      <li>
        <?= Html::encode(
          Yii::t('app', '{from} - {to}', [
            'from' => $fmt->asDateTime($period->start_at, 'short'),
            'to' => $fmt->asDateTime($period->end_at, 'short'),
          ]),
        ) . "\n" ?>
      </li>
<?php } ?>
    </ul>
  </div>

  <div class="mb-3">
    <?= $this->render('event3/stats-info', ['samples' => $samples]) . "\n" ?>
  </div>

<?php if ($samples > 0) { ?>
  <?= $this->render('event3/table', [
    'provider' => $weaponsProvider,
    'samples' => $samples,
  ]) . "\n" ?>
  <?= $this->render('event3/table', [
    'provider' => $specialProvider,
    'samples' => $samples,
  ]) . "\n" ?>
<?php } ?>
</div>
