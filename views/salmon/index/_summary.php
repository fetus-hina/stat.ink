<?php

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\BattleSummaryItemWidget;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var stdClass $summary
 */

$fmt = Yii::createObject([
    'class' => Formatter::class,
    'nullDisplay' => 'N/A',
]);

$cleared = function (?int $clearCount) use ($summary): ?float {
    if ($summary['has_result'] < 1) {
        return null;
    }

    return $clearCount / $summary['has_result'];
};
?>
<div class="row battles-summary mb-3">
  <div class="col-xs-12">
    <div class="user-label"><?= Html::encode(Yii::t('app', 'Summary: Based on the current filter')) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Jobs')) ?></div>
    <div class="user-number"><?= $fmt->asInteger($summary['count']) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Avg. Waves')) ?></div>
    <div class="user-number"><?= $fmt->asDecimal($summary['avg_waves'], 2) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Clear %')) ?></div>
    <div class="user-number"><?= $fmt->asPercent($cleared($summary['w3_cleared']), 1) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 2])) ?></div>
    <div class="user-number"><?= $fmt->asPercent($cleared($summary['w2_cleared']), 1) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 1])) ?></div>
    <div class="user-number"><?= $fmt->asPercent($cleared($summary['w1_cleared']), 1) ?></div>
  </div>
</div>
<div class="row battles-summary mb-3">
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Golden')) ?></div>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary['avail_golden'],
        'total' => $summary['total_golden'],
        'min' => $summary['min_golden'],
        'max' => $summary['max_golden'],
        'median' => $summary['median_golden'],
        'q1' => $summary['q1_4_golden'],
        'q3' => $summary['q3_4_golden'],
        'pct5' => $summary['pct5_golden'],
        'pct95' => $summary['pct95_golden'],
        'stddev' => $summary['stddev_golden'],
        'tooltipText' => '{number, plural, =1{1 egg} other{# eggs}} in {battle, plural, =1{1 shift} other{# shifts}}',
        'summary' => Yii::t('app-salmon2', 'Golden'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Pwr Eggs')) ?></div>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary['avail_power'],
        'total' => $summary['total_power'],
        'min' => $summary['min_power'],
        'max' => $summary['max_power'],
        'median' => $summary['median_power'],
        'q1' => $summary['q1_4_power'],
        'q3' => $summary['q3_4_power'],
        'pct5' => $summary['pct5_power'],
        'pct95' => $summary['pct95_power'],
        'stddev' => $summary['stddev_power'],
        'tooltipText' => '{number, plural, =1{1 egg} other{# eggs}} in {battle, plural, =1{1 shift} other{# shifts}}',
        'summary' => Yii::t('app-salmon2', 'Pwr Eggs'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Rescued')) ?></div>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary['avail_rescue'],
        'total' => $summary['total_rescue'],
        'min' => $summary['min_rescue'],
        'max' => $summary['max_rescue'],
        'median' => $summary['median_rescue'],
        'q1' => $summary['q1_4_rescue'],
        'q3' => $summary['q3_4_rescue'],
        'pct5' => $summary['pct5_rescue'],
        'pct95' => $summary['pct95_rescue'],
        'stddev' => $summary['stddev_rescue'],
        'tooltipText' => '{number, plural, =1{1 time} other{# times}} in {battle, plural, =1{1 shift} other{# shifts}}',
        'summary' => Yii::t('app-salmon2', 'Rescued'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Deaths')) ?></div>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary['avail_death'],
        'total' => $summary['total_death'],
        'min' => $summary['min_death'],
        'max' => $summary['max_death'],
        'median' => $summary['median_death'],
        'q1' => $summary['q1_4_death'],
        'q3' => $summary['q3_4_death'],
        'pct5' => $summary['pct5_death'],
        'pct95' => $summary['pct95_death'],
        'stddev' => $summary['stddev_death'],
        'tooltipText' => '{number, plural, =1{1 time} other{# times}} in {battle, plural, =1{1 shift} other{# shifts}}',
        'summary' => Yii::t('app-salmon2', 'Deaths'),
      ]) . "\n" ?>
    </div>
  </div>
</div>
