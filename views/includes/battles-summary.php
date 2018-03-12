<?php
use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
$this->registerCss('.battles-summary{margin-bottom:15px}');

$fmt = Yii::$app->formatter;
?>
<div class="row battles-summary">
<?php if (isset($headingText) && $headingText != ''): ?>
  <div class="col-xs-12">
    <div class="user-label">
      <?= Html::encode($headingText) . "\n" ?>
    </div>
  </div>
<?php endif; ?>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Battles')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if (isset($link) && $link): ?>
      <?= Html::a(
        Html::encode($fmt->asInteger($summary->battle_count)),
        $link
      ) . "\n" ?>
<?php else: ?>
      <?= Html::encode($fmt->asInteger($summary->battle_count)) . "\n" ?>
<?php endif; ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Win %')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if ($summary->wp === null): ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php else: ?>
      <?= Html::encode($fmt->asDecimal($summary->wp, 1)) . "%\n" ?>
<?php endif; ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', '24H Win %')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if (isset($summary->win_short) && isset($summary->battle_count_short) && $summary->battle_count_short > 0): ?>
      <?= Html::tag(
        'span',
        Html::encode($fmt->asPercent($summary->win_short / $summary->battle_count_short, 1)),
        [
          'class' => 'auto-tooltip',
          'title' => sprintf('%s / %s', $fmt->asInteger($summary->win_short), $fmt->asInteger($summary->battle_count_short)),
        ]
      ) . "\n" ?>
<?php elseif ($summary->wp_short === null): ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php else: ?>
      <?= Html::encode($fmt->asDecimal($summary->wp_short, 1)) . "%\n" ?>
<?php endif; ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Kills')) . "\n" ?>
    </div>
    <div class="user-number">
      <?= $this->render('/includes/_battles-summary-kill-death', [
        'battles' => $summary->kd_present ?? null,
        'total' => $summary->total_kill ?? null,
        'min' => $summary->min_kill ?? null,
        'max' => $summary->max_kill ?? null,
        'median' => $summary->median_kill ?? null,
        'q1' => $summary->q1_4_kill ?? null,
        'q3' => $summary->q3_4_kill ?? null,
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
      <?= $this->render('/includes/_battles-summary-kill-death', [
        'battles' => $summary->kd_present ?? null,
        'total' => $summary->total_death ?? null,
        'min' => $summary->min_death ?? null,
        'max' => $summary->max_death ?? null,
        'median' => $summary->median_death ?? null,
        'q1' => $summary->q1_4_death ?? null,
        'q3' => $summary->q3_4_death ?? null,
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
<?php if ($summary->kd_present > 0): ?>
<?php   if ($summary->total_death == 0): ?>
<?php     if ($summary->total_kill == 0): ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php     else: ?>
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
<?php     endif; ?>
<?php   else: ?>
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
<?php   endif; ?>
<?php else: ?>
        -
<?php endif; ?>
    </div>
  </div>
</div>
