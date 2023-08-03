<?php

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Event3;
use app\models\Event3StatsPower;
use app\models\Event3StatsPowerHistogram;
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
 * @var Event3StatsPower $abstract
 * @var Event3StatsPowerHistogram[] $histogram
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
    <p class="mb-1 text-muted">
      <?= Html::encode(Yii::t('db/event3/description', $event->desc)) . "\n" ?>
    </p>
    <ul class="mb-1">
      <li>
        <?= Html::encode(Yii::t('app-rule3', $schedule->rule->name ?? '?')) . "\n" ?>
      </li>
<?php foreach ($periods as $i => $period) { ?>
      <li>
        <?= Html::encode(mb_chr(0x2460 + $i, 'UTF-8')) . "\n" ?>
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

<?php if ($abstract) { ?>
  <aside class="mb-3">
    <div class="panel panel-default">
      <div class="panel-heading">
        <?= Html::encode(
          Yii::t('app', 'Challenge Power Distribution'),
        ) . "\n" ?>
      </div>
      <div class="panel-body pb-0">
        <div class="alert alert-danger mb-3">
          <p class="small my-0">
            <?= Html::encode(
              Yii::t(
                'app',
                'This data is based on {siteName} users and differs significantly from overall game statistics.',
                [
                  'siteName' => Yii::$app->name,
                ],
              ),
            ) . "\n" ?>
          </p>
        </div>
        <div class="table-responsive mb-3">
          <table class="table table-bordered table-striped table-condensed w-auto mb-0">
            <thead>
              <tr>
                <th></th>
                <th class="text-center"><?= Html::encode(Yii::t('app', 'Users')) ?></th>
                <th class="text-center"><?= Html::encode(Yii::t('app', 'Battles')) ?></th>
                <th class="text-center"><?= Html::encode(Yii::t('app', 'Average')) ?></th>
                <th class="text-center"><?= Html::encode(Yii::t('app', 'Std Dev')) ?></th>
                <th class="text-center">
                  <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 75])) . "\n" ?>
                </th>
                <th class="text-center ">
                  <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 50])) . "\n" ?>
                </th>
                <th class="text-center">
                  <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 25])) . "\n" ?>
                </th>
                <th class="text-center">
                  <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 20])) . "\n" ?>
                </th>
                <th class="text-center">
                  <?= Html::encode(Yii::t('app', 'Top {percentile}%', ['percentile' => 5])) . "\n" ?>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class="text-center" scope="row"><?= Html::encode(Yii::t('app', 'Total')) ?></th>
                <td class="text-center"><?= Html::encode($fmt->asInteger($abstract->users)) ?></td>
                <td class="text-center">
                  <?= Html::encode($fmt->asInteger($abstract->agg_battles)) . "\n" ?>
                  <?= Html::tag(
                    'span',
                    vsprintf('(%s)', [
                      Html::encode($fmt->asInteger($abstract->battles)),
                    ]),
                    [
                      'class' => 'auto-tooltip',
                      'title' => Yii::t('app', 'Includes battles with unknown event power'),
                    ],
                  ) . "\n" ?>
                </td>
                <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->average, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($abstract->stddev, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($abstract->p25, 1)) ?></td>
                <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->p50, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($abstract->p75, 1)) ?></td>
                <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->p80, 1)) ?></td>
                <td class="text-center fw-bold"><?= Html::encode($fmt->asDecimal($abstract->p95, 1)) ?></td>
              </tr>
<?php foreach ($periods as $i => $period) { ?>
              <tr class="text-muted">
                <th class="text-center" scope="row"><?= Html::encode(mb_chr(0x2460 + $i, 'UTF-8')) ?></th>
<?php if ($row = $periodAbstracts[$period->id] ?? null) { ?>
                <td class="text-center"><?= Html::encode($fmt->asInteger($row->users)) ?></td>
                <td class="text-center">
                  <?= Html::encode($fmt->asInteger($row->agg_battles)) . "\n" ?>
                  <?= Html::tag(
                    'span',
                    vsprintf('(%s)', [
                      Html::encode($fmt->asInteger($row->battles)),
                    ]),
                    [
                      'class' => 'auto-tooltip',
                      'title' => Yii::t('app', 'Includes battles with unknown event power'),
                    ],
                  ) . "\n" ?>
                </td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->average, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->stddev, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p25, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p50, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p75, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p80, 1)) ?></td>
                <td class="text-center"><?= Html::encode($fmt->asDecimal($row->p95, 1)) ?></td>
<?php } else { ?>
<?php for ($j = 0; $j < 9; ++$j) { ?>
                <td class="text-center">-</td>
<?php } ?>
<?php } ?>
              </tr>
<?php } ?>
            </tbody>
          </table>
        </div>
<?php if ($abstract->histogram_width > 0 && $histogram) { ?>
        <div class="mb-3">
          <?= $this->render('event3/histogram', [
            'abstract' => $abstract,
            'histogram' => ArrayHelper::map($histogram, 'class_value', 'battles'),
          ]) . "\n" ?>
        </div>
<?php } ?>
      </div>
    </div>
  </aside>
<?php } ?>

<?php if ($samples > 0) { ?>
  <?= $this->render('event3/table', [
    'provider' => $weaponsProvider,
    'samples' => $samples,
  ]) . "\n" ?>
  <?= $this->render('event3/table', [
    'provider' => $specialProvider,
    'samples' => $samples,
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
