<?php

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\EventTrait;
use app\components\widgets\Icon;
use app\models\Map3;
use app\models\SalmonEvent3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type EventStats from EventTrait
 *
 * @var EventStats $eventStats
 * @var Map3|SalmonMap3|null $map
 * @var User $user
 * @var View $this
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonWaterLevel2> $tides
 */

if (!$eventStats) {
  return;
}

$fmt = Yii::$app->formatter;

?>
<h3><?= Html::encode(Yii::t('app-salmon3', 'Water Level and Events')) ?></h3>
<div class="table-responsive">
  <table class="table table-bordered table-condensed table-striped mb-0">
    <thead>
      <tr>
        <th class="text-center" rowspan="2"><?= Html::encode(Yii::t('app-salmon3', 'Known Occurrence')) ?></th>
<?php foreach ($tides as $tide) { ?>
        <th class="text-center" colspan="4">
          <?= Icon::s3SalmonTide($tide) . "\n" ?>
          <?= Html::encode(Yii::t('app-salmon-tide2', $tide->name)) . "\n" ?>
        </th>
<?php } ?>
      </tr>
      <tr>
<?php foreach ($tides as $tide) { ?>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Waves')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon3', 'Occur %')) ?></th>
        <th class="text-center"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></th>
        <th class="text-center"><?= Icon::goldenEgg() ?></th>
<?php } ?>
      </tr>
    </thead>
    <tbody>
      <?= $this->render('events/total-row', compact('eventStats', 'tides')) . "\n" ?>
      <?= $this->render('events/event-row', [
        'event' => null,
        'eventStats' => $eventStats,
        'tides' => $tides,
      ]) . "\n" ?>
<?php foreach ($events as $event) { ?>
      <?= $this->render('events/event-row', compact('event', 'eventStats', 'tides')) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
<?= Html::tag(
  'p',
  implode(' ', array_filter([
    $map
      ? Yii::t(
        'app-salmon3',
        'Note that this data is too small data size to speak of occurrence rates.',
      )
      : null,
    Yii::t('app-salmon3', 'For a more accurate occurrence rate, see {link}.', [
      'link' => Html::a(
        Yii::t('app-salmon3', 'Water Level and Events'),
        ['entire/salmon3-tide',
          '#' => $map
            ? sprintf('event-%s', $map->key)
            : null,
        ],
      ),
    ]),
  ])),
  [
    'class' => [
      'mb-3',
      'mt-1',
      'small',
      'text-muted',
    ],
  ],
) . "\n" ?>
