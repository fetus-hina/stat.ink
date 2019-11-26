<?php

declare(strict_types=1);

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
// $pct5
// $pct95
// $stddev
// $tooltipText
// $summary

BattleSummaryDialogAsset::register($this);

$fmt = Yii::$app->formatter;
?>
<?php if ($battles > 0) { ?>
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
<?php if (
  ($min !== null) &&
  ($max !== null) &&
  ($median !== null) &&
  ($q1 !== null) &&
  ($q3 !== null) &&
  ($pct5 !== null) &&
  ($pct95 !== null)
) { ?>
  <?= Html::a($content, null, [
    'class' => 'summary-box-plot text-link',
    'data' => [
      'stats' => Json::encode([
        'min' => (int)$min,
        'q1'  => (float)$q1,
        'q2'  => (float)$median,
        'q3'  => (float)$q3,
        'max' => (int)$max,
        'pct5' => (float)$pct5,
        'pct95' => (float)$pct95,
        'avg' => $total / $battles,
        'stddev' => isset($stddev) ? (float)$stddev : null,
      ]),
      'disp' => Json::encode([
        'min' => $fmt->asInteger((int)$min),
        'q1'  => $fmt->asDecimal((float)$q1, 2),
        'q2'  => $fmt->asDecimal((float)$median, 2),
        'q3'  => $fmt->asDecimal((float)$q3, 2),
        'max' => $fmt->asInteger((int)$max),
        'pct5' => $fmt->asDecimal((float)$pct5, 2),
        'pct95' => $fmt->asDecimal((float)$pct95, 2),
        'avg' => $fmt->asDecimal($total / $battles, 2),
        'stddev' => $stddev ? $fmt->asDecimal($stddev, 3) : null,
        'iqr' => $fmt->asDecimal($q3 - $q1, 2),
        'title' => $summary ?? null,
      ]),
    ],
  ]) . "\n" ?>
<?php } else { ?>
  <?= $content . "\n" ?>
<?php } ?>
<?php } else { ?>
  <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php } ?>
