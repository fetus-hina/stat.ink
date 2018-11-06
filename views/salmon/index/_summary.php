<?php
declare(strict_types=1);

use app\components\i18n\Formatter;
use yii\helpers\Html;

$this->registerCss('.battles-summary{margin-bottom:15px}');

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
<div class="row battles-summary">
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
<div class="row battles-summary">
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Golden')) ?></div>
    <div class="user-number"><?= $fmt->asDecimal($summary['avg_golden'], 2) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Pwr Eggs')) ?></div>
    <div class="user-number"><?= $fmt->asDecimal($summary['avg_power'], 2) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Rescued')) ?></div>
    <div class="user-number"><?= $fmt->asDecimal($summary['avg_rescue'], 2) ?></div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label"><?= Html::encode(Yii::t('app-salmon2', 'Deaths')) ?></div>
    <div class="user-number"><?= $fmt->asDecimal($summary['avg_death'], 2) ?></div>
  </div>
</div>
