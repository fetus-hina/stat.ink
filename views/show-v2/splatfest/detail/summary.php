<?php

/**
 * @copyright Copyright (C) 2021-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\BattleSummaryItemWidget;
use yii\helpers\Html;

$f = Yii::$app->formatter;

$label = fn($text) => Html::tag(
  'div',
  Html::tag('span', Html::encode($text), ['class' => 'auto-tooltip', 'title' => $text]),
  ['class' => 'user-label']
);

?>
<div class="row battles-summary">
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Battles')) . "\n" ?>
    <div class="user-number"><?= $f->asInteger($summary->count) ?></div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Win %')) . "\n" ?>
    <div class="user-number"><?= ($summary->win + $summary->lose > 0)
      ? $f->asPercent($summary->win / ($summary->win + $summary->lose), 1)
      : Html::encode(Yii::t('app', 'N/A'))
    ?></div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Avg Kills')) . "\n" ?>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary->kd_present ?? null,
        'total' => $summary->total_kill ?? null,
        'min' => $summary->min_kill ?? null,
        'max' => $summary->max_kill ?? null,
        'median' => $summary->median_kill ?? null,
        'q1' => $summary->q1_4_kill ?? null,
        'q3' => $summary->q3_4_kill ?? null,
        'pct5' => $summary->pct5_kill ?? null,
        'pct95' => $summary->pct95_kill ?? null,
        'stddev' => $summary->stddev_kill ?? null,
        'tooltipText' => '{number, plural, =1{1 kill} other{# kills}} in {battle, plural, =1{1 battle} other{# battles}}',
        'summary' => Yii::t('app', 'Kills'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Avg Deaths')) . "\n" ?>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary->kd_present ?? null,
        'total' => $summary->total_death ?? null,
        'min' => $summary->min_death ?? null,
        'max' => $summary->max_death ?? null,
        'median' => $summary->median_death ?? null,
        'q1' => $summary->q1_4_death ?? null,
        'q3' => $summary->q3_4_death ?? null,
        'pct5' => $summary->pct5_death ?? null,
        'pct95' => $summary->pct95_death ?? null,
        'stddev' => $summary->stddev_death ?? null,
        'tooltipText' => '{number, plural, =1{1 death} other{# deaths}} in {battle, plural, =1{1 battle} other{# battles}}',
        'summary' => Yii::t('app', 'Deaths'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Kill Ratio')) . "\n" ?>
    <div class="user-number"><?php
      if ($summary->total_death == 0) {
        if ($summary->total_kill > 0) {
          echo $f->asDecimal(99.99, 2);
        } else {
          echo Html::encode(Yii::t('app', 'N/A'));
        }
      } else {
        echo Html::tag(
          'span',
          $f->asDecimal($summary->total_kill / $summary->total_death, 2),
          [
            'class' => 'auto-tooltip',
            'title' => vsprintf('%s: %s', [
              Yii::t('app', 'Kill Rate'),
              $f->asPercent(
                $summary->total_kill / ($summary->total_kill + $summary->total_death),
                1
              ),
            ]),
          ]
        );
      }
    ?></div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Avg Inked')) . "\n" ?>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary->inked_present ?? null,
        'total' => $summary->total_inked ?? null,
        'min' => $summary->min_inked ?? null,
        'max' => $summary->max_inked ?? null,
        'median' => $summary->median_inked ?? null,
        'q1' => $summary->q1_4_inked ?? null,
        'q3' => $summary->q3_4_inked ?? null,
        'pct5' => $summary->pct5_inked ?? null,
        'pct95' => $summary->pct95_inked ?? null,
        'stddev' => $summary->stddev_inked ?? null,
        'summary' => Yii::t('app', 'Avg Inked'),
      ]) . "\n" ?>
    </div>
  </div>
<?php if ($summary->is_v4) { ?>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Fest Power (Normal)')) . "\n" ?>
    <div class="user-number"><?= ($summary->fest_power_v4_normal)
      ? Yii::t('app', '~{estPower}', [
        'estPower' => $f->asInteger(round($summary->fest_power_v4_normal / 10) * 10),
      ])
      : '-'
    ?></div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Fest Power (Pro)')) . "\n" ?>
    <div class="user-number"><?= ($summary->fest_power_v4_pro)
      ? $f->asInteger($summary->fest_power_v4_pro)
      : '-'
    ?></div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Clout (Normal)')) . "\n" ?>
    <div class="user-number"><?= ($summary->clout_normal)
      ? $f->asInteger($summary->clout_normal)
      : '-'
    ?></div>
  </div>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Clout (Pro)')) . "\n" ?>
    <div class="user-number"><?= ($summary->clout_pro)
      ? $f->asInteger($summary->clout_pro)
      : '-'
    ?></div>
  </div>
<?php } else { ?>
  <div class="col-xs-4 col-md-2 mb-3">
    <?= $label(Yii::t('app', 'Fest Power')) . "\n" ?>
    <div class="user-number"><?= ($summary->fest_power_v1)
      ? $f->asInteger($summary->fest_power_v1)
      : '-'
    ?></div>
  </div>
<?php } ?>
</div>
