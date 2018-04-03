<?php
use app\models\SalmonSchedule2;
use app\models\Schedule2;
use yii\helpers\Html;

$now = (new DateTimeImmutable())
    ->setTimestamp($_SERVER['REQUEST_TIME'])
    ->setTimeZone(new DateTimeZone(Yii::$app->timeZone));

$schedule = Schedule2::getInfo();
$current = $schedule->current ?? null;
$next = $schedule->next ?? null;
$currentScheduleAvailable = ($current && (
    ($current->regular ?? null) ||
    ($current->gachi ?? null) ||
    ($current->league ?? null)
));

$salmonSchedules = SalmonSchedule2::find()
    ->with(['map', 'weapons.weapon'])
    ->andWhere(['>=', 'end_at', $now->format(DateTime::ATOM)])
    ->orderBy(['end_at' => SORT_ASC])
    ->limit(2)
    ->all();

if ($currentScheduleAvailable): 
?>
<h2>
  <?= Html::encode(Yii::t('app', 'Schedule')) . "\n" ?>
</h2>
<ul class="nav nav-tabs" role="tablist" id="schedule-tab">
  <li role="presentation" class="active">
    <a href="#schedule-regular" data-toggle="tab"><?=
      Html::encode(Yii::t('app-rule2', 'Regular'))
    ?></a>
  </li>
  <li role="presentation">
    <a href="#schedule-ranked" data-toggle="tab"><?=
      Html::encode(Yii::t('app-rule2', 'Ranked'))
    ?></a>
  </li>
  <li role="presentation">
    <a href="#schedule-league" data-toggle="tab"><?=
      Html::encode(Yii::t('app-rule2', 'League'))
    ?></a>
  </li>
<?php if ($salmonSchedules): ?>
  <li role="presentation">
    <a href="#schedule-salmon" data-toggle="tab"><?=
      Html::encode(Yii::t('app-salmon2', 'Salmon Run'))
    ?></a>
  </li>
<?php endif ?>
</ul>
<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="schedule-regular">
    <?= $this->render('_index_schedule_rule', [
      'data' => [
        [
          'term' => $current->_t ?? null,
          'data' => $current->regular ?? null,
        ],
        [
          'term' => $next->_t ?? null,
          'data' => $next->regular ?? null,
        ],
      ],
    ]) . "\n" ?>
  </div>
  <div role="tabpanel" class="tab-pane" id="schedule-ranked">
    <?= $this->render('_index_schedule_rule', [
      'data' => [
        [
          'term' => $current->_t ?? null,
          'data' => $current->gachi ?? null,
        ],
        [
          'term' => $next->_t ?? null,
          'data' => $next->gachi ?? null,
        ],
      ],
    ]) . "\n" ?>
  </div>
  <div role="tabpanel" class="tab-pane" id="schedule-league">
    <?= $this->render('_index_schedule_rule', [
      'data' => [
        [
          'term' => $current->_t ?? null,
          'data' => $current->league ?? null,
        ],
        [
          'term' => $next->_t ?? null,
          'data' => $next->league ?? null,
        ],
      ],
    ]) . "\n" ?>
  </div>
<?php if ($salmonSchedules): ?>
  <div role="tabpanel" class="tab-pane" id="schedule-salmon">
    <?= $this->render('_index_schedule_salmon', [
      'schedules' => $salmonSchedules,
    ]) . "\n" ?>
  </div>
<?php endif ?>
</div>
<p class="text-right">
  <?= Yii::t('app', 'Source: {source}', [
    'source' => Html::a(Html::encode('Splatoon2.ink'), 'https://splatoon2.ink/'),
  ]) . "\n" ?>
</p>
<?php endif ?>
