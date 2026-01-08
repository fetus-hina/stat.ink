<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\components\widgets\SnsWidget;
use app\models\Event3;
use app\models\Event3StatsPower;
use app\models\Event3StatsPowerHistogram;
use app\models\Event3StatsPowerPeriodHistogram;
use app\models\EventPeriod3;
use app\models\EventSchedule3;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var ActiveDataProvider $specialProvider
 * @var ActiveDataProvider $weaponsProvider
 * @var Event3 $event
 * @var Event3StatsPower $abstract
 * @var Event3StatsPowerHistogram[] $histogram
 * @var Event3StatsPowerPeriodHistogram[] $periodHistogram
 * @var Event3[] $events
 * @var EventSchedule3 $schedule
 * @var EventSchedule3[] $schedules
 * @var View $this
 * @var array<int, Event3StatsPowerPeriod> $periodAbstracts
 */

$title = vsprintf('%s - %s', [
  Yii::t('app', 'Weapons'),
  Yii::t('app-lobby3', 'Challenge'),
]);
$this->title = vsprintf('%s | %s', [
  $title,
  Yii::$app->name,
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
    <p class="mb-1">
      <?= vsprintf('%s: %s %s', [
        Html::encode(Yii::t('app', 'Mode')),
        Icon::s3Rule($schedule->rule),
        Html::encode(Yii::t('app-rule3', $schedule->rule->name ?? '?')),
      ]) . "\n" ?>
    </p>
    <p class="mb-1 text-muted">
      <?= Html::encode(Yii::t('db/event3/description', $event->desc)) . "\n" ?>
    </p>
<?php if ($periods) { ?>
    <ul class="mb-1">
<?php foreach ($periods as $i => $period) { ?>
      <li>
        <?= Html::encode(mb_chr(0x2460 + $i, 'UTF-8')) ?>:
        <?= Html::encode(
          Yii::t('app', '{from} - {to}', [
            'from' => $fmt->asDateTime($period->start_at, 'short'),
            'to' => $fmt->asDateTime($period->end_at, 'short'),
          ]),
        ) . "\n" ?>
      </li>
<?php } ?>
    </ul>
<?php } ?>
  </div>

  <aside class="mb-3">
    <?= $this->render('event3/power-distrib-card', compact(
      'abstract',
      'histogram',
      'periodAbstracts',
      'periodHistogram',
      'schedule',
    )) . "\n" ?>
  </aside>

  <hr>
  <h3 class="mb-3">
    <?= Html::encode(
      Yii::t('app', 'Weapon Stats'),
    ) . "\n" ?>
  </h3>
  <div class="mb-3">
    <?= $this->render('event3/stats-info', ['samples' => $samples]) . "\n" ?>
  </div>

<?php if ($samples > 0) { ?>
  <?= Tabs::widget([
    'items' => [
      [
        'active' => true,
        'label' => Yii::t('app', 'Detailed'),
        'content' => $this->render('event3/table', [
          'provider' => $weaponsProvider,
          'samples' => $samples,
        ]),
      ],
      [
        'label' => Yii::t('app', 'Win %'),
        'content' => $this->render('event3/win-rate', [
          'models' => $weaponsProvider->getModels(),
        ]),
      ],
    ],
    'tabContentOptions' => [
      'class' => 'mt-3 tab-content'
    ],
  ]) . "\n" ?>
  <?= Tabs::widget([
    'items' => [
      [
        'active' => true,
        'label' => Yii::t('app', 'Detailed'),
        'content' => $this->render('event3/table', [
          'provider' => $specialProvider,
          'samples' => $samples,
        ]),
      ],
      [
        'label' => Yii::t('app', 'Win %'),
        'content' => $this->render('event3/win-rate', [
          'models' => $specialProvider->getModels(),
        ]),
      ],
    ],
    'tabContentOptions' => [
      'class' => 'mt-3 tab-content'
    ],
  ]) . "\n" ?>
<?php } elseif ($event->internal_id === 'TGVhZ3VlTWF0Y2hFdmVudC1QYWlyQ3Vw') { ?>
<?php // 「最強ペア決定戦」は現在のところデータがないので、お詫びを表示する ?>
<?php // See: https://twitter.com/fetus_hina/status/1685080696757583872 ?>
  <div class="alert alert-danger mb-3">
    <p class="mt-0 mb-3"><strong>Data has not been created</strong></p>
    <p class="mt-0 mb-3">
      <em>Short:</em> Due to the special conditions of this Challenge, we are unable to tally the
      results at this time.
    </p>
    <p class="mt-0 mb-0">
      <em>Long:</em><br>
      <?= Html::encode(Yii::$app->name) ?> is designed to flag "available for aggregation" only when
      certain conditions are met.<br>
      Because this Challenge is run under the special 2 vs. 2 rule, one of the conditions,
      "<?= Html::a(
        'the match was played with 8 players',
        'https://github.com/fetus-hina/stat.ink/blob/0a23148f6d56b098352bc53e0be431e1efaa2216/models/api/v3/PostBattleForm.php#L1014',
        [
          'class' => 'alert-link',
          'rel' => 'noopener noreferrer',
          'target' => '_blank',
        ],
      ) ?>," is always false.<br>
      So now the aggregation program has determined that all matches are unusable for statistics,
      which causes "no data".
    </p>
  </div>
<?php } ?>
</div>
