<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\AppAsset;
use app\components\widgets\BattleSummaryItemWidget;
use yii\helpers\Html;

AppAsset::register($this);

$fmt = Yii::$app->formatter;
?>
<div class="row battles-summary mb-3">
<?php if (isset($headingText) && $headingText != '') { ?>
  <div class="col-xs-12">
    <div class="user-label">
      <?= Html::encode($headingText) . "\n" ?>
    </div>
  </div>
<?php }  ?>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Battles')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if (isset($link) && $link) { ?>
      <?= Html::a(
        Html::encode($fmt->asInteger($summary->battle_count)),
        $link,
        ['data' => ['pjax' => '0']],
      ) . "\n" ?>
<?php } else { ?>
      <?= Html::encode($fmt->asInteger($summary->battle_count)) . "\n" ?>
<?php } ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Win %')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if ($summary->wp === null) { ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php } else { ?>
      <?= Html::encode($fmt->asDecimal($summary->wp, 1)) . "%\n" ?>
<?php } ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', '24H Win %')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if (isset($summary->win_short) && isset($summary->battle_count_short) && $summary->battle_count_short > 0) { ?>
      <?= Html::tag(
        'span',
        Html::encode($fmt->asPercent($summary->win_short / $summary->battle_count_short, 1)),
        [
          'class' => 'auto-tooltip',
          'title' => sprintf('%s / %s', $fmt->asInteger($summary->win_short), $fmt->asInteger($summary->battle_count_short)),
        ]
      ) . "\n" ?>
<?php } elseif ($summary->wp_short === null) { ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php } else { ?>
      <?= Html::encode($fmt->asDecimal($summary->wp_short, 1)) . "%\n" ?>
<?php } ?>
    </div>
  </div>
<?php if (($summary->assist_present ?? null) > 0 || ($summary->special_present ?? null) > 0 || ($summary->inked_present ?? null) > 0) { ?>
</div>
<div class="row battles-summary mb-3">
<?php } ?>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Kills')) . "\n" ?>
    </div>
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
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Deaths')) . "\n" ?>
    </div>
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
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Kill Ratio')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if ($summary->kd_present > 0) { ?>
<?php   if ($summary->total_death == 0) { ?>
<?php     if ($summary->total_kill == 0) { ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php     } else { ?>
      <?= Html::tag(
        'span',
        Html::encode('∞'),
        [
          'class' => 'auto-tooltip',
          'title' => sprintf(
            '%s: %s',
            Yii::t('app', 'Kill Rate'),
            $fmt->asPercent(1.0, 1)
          ),
        ]
      ) . "\n" ?>
<?php     } ?>
<?php   } else { ?>
      <?= Html::tag(
        'span',
        Html::encode(
          $fmt->asDecimal(
            $summary->total_kill / $summary->total_death,
            2
          )
        ),
        [
          'class' => 'auto-tooltip',
          'title' => sprintf(
            '%s: %s',
            Yii::t('app', 'Kill Rate'),
            $fmt->asPercent(
              $summary->total_kill / ($summary->total_kill + $summary->total_death),
              1
            )
          ),
        ]
      ) . "\n" ?>
<?php   } ?>
<?php } else { ?>
        -
<?php } ?>
    </div>
  </div>
<?php if (($summary->assist_present ?? null) > 0 || ($summary->special_present ?? null) > 0 || ($summary->inked_present ?? null) > 0) { ?>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Assists')) . "\n" ?>
    </div>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary->assist_present ?? null,
        'total' => $summary->total_assist ?? null,
        'min' => $summary->min_assist ?? null,
        'max' => $summary->max_assist ?? null,
        'median' => $summary->median_assist ?? null,
        'q1' => $summary->q1_4_assist ?? null,
        'q3' => $summary->q3_4_assist ?? null,
        'pct5' => $summary->pct5_assist ?? null,
        'pct95' => $summary->pct95_assist ?? null,
        'stddev' => $summary->stddev_assist ?? null,
        'summary' => Yii::t('app', 'Assists'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Specials')) . "\n" ?>
    </div>
    <div class="user-number">
      <?= BattleSummaryItemWidget::widget([
        'battles' => $summary->special_present ?? null,
        'total' => $summary->total_special ?? null,
        'min' => $summary->min_special ?? null,
        'max' => $summary->max_special ?? null,
        'median' => $summary->median_special ?? null,
        'q1' => $summary->q1_4_special ?? null,
        'q3' => $summary->q3_4_special ?? null,
        'pct5' => $summary->pct5_special ?? null,
        'pct95' => $summary->pct95_special ?? null,
        'stddev' => $summary->stddev_special ?? null,
        'summary' => Yii::t('app', 'Specials'),
      ]) . "\n" ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Inked')) . "\n" ?>
    </div>
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
        'summary' => Yii::t('app', 'Inked'),
      ]) . "\n" ?>
    </div>
  </div>
<?php } ?>
</div>
