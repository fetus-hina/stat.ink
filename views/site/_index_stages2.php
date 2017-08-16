<?php
use app\models\Schedule2;
use yii\helpers\Html;

$timeFormat = Yii::$app->language === 'en-US' ? 'g:i A' : 'H:i';
$schedule = Schedule2::getInfo();
?>
<h2>
  <span class="hidden-xs"><?= Html::encode(Yii::t('app', 'Current Stages')) ?></span>
  <?= implode('', [
    Html::tag('span', '[', ['class' => 'hidden-xs']),
    Html::encode(sprintf(
      '%s-%s',
      date($timeFormat, $schedule->current->_t[0]),
      date($timeFormat, $schedule->current->_t[1])
    )),
    Html::tag('span', ']', ['class' => 'hidden-xs']),
  ]) . "\n" ?>
</h2>
<div class="row">
  <div class="col-xs-12 col-lg-4">
    <?= $this->render('_index_stages_rule2', ['mode' => 'Regular', 'data' => $schedule->current->regular ?? null]) . "\n" ?>
  </div>
  <div class="col-xs-12 col-lg-4">
    <?= $this->render('_index_stages_rule2', ['mode' => 'Ranked', 'data' => $schedule->current->gachi ?? null]) . "\n" ?>
  </div>
  <div class="col-xs-12 col-lg-4">
    <?= $this->render('_index_stages_rule2', ['mode' => 'League', 'data' => $schedule->current->league ?? null]) . "\n" ?>
  </div>
</div>
<?php if ($schedule->next->regular || $schedule->next->gachi || $schedule->next->league): ?>
<h2>
  <span class="hidden-xs"><?= Html::encode(Yii::t('app', 'Next Stages')) ?></span>
  <?= implode('', [
    Html::tag('span', '[', ['class' => 'hidden-xs']),
    Html::encode(sprintf(
      '%s-%s',
      date($timeFormat, $schedule->next->_t[0]),
      date($timeFormat, $schedule->next->_t[1])
    )),
    Html::tag('span', ']', ['class' => 'hidden-xs']),
  ]) . "\n" ?>
</h2>
<div class="row">
  <div class="col-xs-12 col-lg-4">
    <?= $this->render('_index_stages_rule2', ['mode' => 'Regular', 'data' => $schedule->next->regular ?? null]) . "\n" ?>
  </div>
  <div class="col-xs-12 col-lg-4">
    <?= $this->render('_index_stages_rule2', ['mode' => 'Ranked', 'data' => $schedule->next->gachi ?? null]) . "\n" ?>
  </div>
  <div class="col-xs-12 col-lg-4">
    <?= $this->render('_index_stages_rule2', ['mode' => 'League', 'data' => $schedule->next->league ?? null]) . "\n" ?>
  </div>
</div>
<?php endif; ?>
