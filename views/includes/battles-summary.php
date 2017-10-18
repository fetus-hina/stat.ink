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
<?php if ($summary->wp_short === null): ?>
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
<?php if ($summary->kd_present > 0): ?>
      <?= Html::tag(
        'span',
        Html::encode(
          $fmt->asDecimal(
            $summary->total_kill / $summary->kd_present,
            2
          )
        ),
        [
          'class' => 'auto-tooltip',
          'title' => isset($summary->median_kill)
            ? Yii::t('app', 'max={max} min={min} median={median}', [
              'max' => $summary->max_kill === null ? '?' : $fmt->asInteger($summary->max_kill),
              'min' => $summary->min_kill === null ? '?' : $fmt->asInteger($summary->min_kill),
              'median' => $summary->median_kill === null ? '?' : $fmt->asDecimal($summary->median_kill, 1),
            ])
            : Yii::t(
              'app',
              '{number, plural, =1{1 kill} other{# kills}} in {battle, plural, =1{1 battle} other{# battles}}',
              [
                'number' => $summary->total_kill,
                'battle' => $summary->kd_present,
              ]
            ),
        ]
      ) . "\n" ?>
<?php else: ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php endif; ?>
    </div>
  </div>
  <div class="col-xs-4 col-md-2">
    <div class="user-label">
      <?= Html::encode(Yii::t('app', 'Avg Deaths')) . "\n" ?>
    </div>
    <div class="user-number">
<?php if ($summary->kd_present > 0): ?>
      <?= Html::tag(
        'span',
        Html::encode(
          $fmt->asDecimal(
            $summary->total_death / $summary->kd_present,
            2
          )
        ),
        [
          'class' => 'auto-tooltip',
          'title' => isset($summary->median_kill)
            ? Yii::t('app', 'max={max} min={min} median={median}', [
              'max' => $summary->max_kill === null ? '?' : $fmt->asInteger($summary->max_kill),
              'min' => $summary->min_kill === null ? '?' : $fmt->asInteger($summary->min_kill),
              'median' => $summary->median_kill === null ? '?' : $fmt->asDecimal($summary->median_kill, 1),
            ])
            : Yii::t(
              'app',
              '{number, plural, =1{1 death} other{# deaths}} in {battle, plural, =1{1 battle} other{# battles}}',
              [
                'number' => $summary->total_death,
                'battle' => $summary->kd_present,
              ]
            ),
        ]
      ) . "\n" ?>
<?php else: ?>
      <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php endif; ?>
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
