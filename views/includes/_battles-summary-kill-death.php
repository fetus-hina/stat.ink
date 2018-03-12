<?php
use app\assets\BattleSummaryDialogAsset;
use yii\helpers\Html;
use yii\helpers\Json;

// $battles
// $total
// $min
// $max
// $q1
// $q3
// $median
// $stddev
// $tooltipText
// $summary

BattleSummaryDialogAsset::register($this);

$fmt = Yii::$app->formatter;
?>
<?php if ($battles > 0): ?>
<?php $content = Html::tag(
  'span',
  Html::encode($fmt->asDecimal($total / $battles, 2)),
  [
    'class' => 'auto-tooltip',
    'title' => isset($median)
      ? Yii::t('app', 'max={max} min={min} median={median}', [
        'max' => $max === null ? '?' : $fmt->asInteger($max),
        'min' => $min === null ? '?' : $fmt->asInteger($min),
        'median' => $median === null ? '?' : $fmt->asDecimal($median, 1),
      ])
      : Yii::t('app', $tooltipText, [
        'number' => $total,
        'battle' => $battles,
      ]),
  ]
) ?>
<?php if ($min !== null && $max !== null && $median !== null && $q1 !== null && $q3 !== null): ?>
  <?= Html::a($content, 'javascript:;', [
    'class' => 'summary-box-plot',
    'data' => [
      'stats' => Json::encode([
        'min' => (int)$min,
        'q1'  => (float)$q1,
        'q2'  => (float)$median,
        'q3'  => (float)$q3,
        'max' => (int)$max,
        'avg' => $total / $battles,
        'stddev' => $stddev ?? null,
      ]),
      'disp' => Json::encode([
        'min' => $fmt->asInteger((int)$min),
        'q1'  => $fmt->asDecimal((float)$q1, 1),
        'q2'  => $fmt->asDecimal((float)$median, 1),
        'q3'  => $fmt->asDecimal((float)$q3, 1),
        'max' => $fmt->asInteger((int)$max),
        'avg' => $fmt->asDecimal($total / $battles, 2),
        'stddev' => $stddev ? $fmt->asDecimal($stddev, 3) : null,
        'title' => $summary ?? null,
      ]),
    ],
  ]) . "\n" ?>
<?php else: ?>
  <?= $content . "\n" ?>
<?php endif ?>
<?php else: ?>
  <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php endif ?>
