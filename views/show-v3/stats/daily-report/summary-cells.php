<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\show\v3\stats\DailyReportAction;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array{
 *   battles: int,
 *   wins: int,
 *   kills_for_ratio: int,
 *   deaths_for_ratio: int,
 *   stats: array<string, array{avg: ?float, min: ?float, max: ?float, med: ?float, mod: ?float, sd: ?float}>
 * } $summary
 */

$fmt = Yii::$app->formatter;

$formatStat = fn (?float $value, int $decimals): string => $value === null
  ? ''
  : $fmt->asDecimal($value, $decimals);

echo Html::tag('td', Html::encode($fmt->asInteger($summary['battles'])), ['class' => 'text-right']) . "\n";

echo Html::tag(
  'td',
  Html::encode(
    $summary['battles'] > 0 ? $fmt->asPercent($summary['wins'] / $summary['battles'], 1) : '',
  ),
  ['class' => 'text-right'],
) . "\n";

foreach (DailyReportAction::getStatsColumns() as $key => $column) {
  $stats = $summary['stats'][$key];
  $decimals = $column['decimals'];
  echo Html::tag(
    'td',
    $stats['avg'] === null ? '' : Html::tag(
      'span',
      Html::encode($formatStat($stats['avg'], $decimals)),
      [
        'class' => 'auto-tooltip',
        'title' => $column['mode']
          ? Yii::t(
            'app-statistics',
            'max={max} min={min} average={avg} median={median} mode={mode} stddev={stddev}',
            [
              'max' => $formatStat($stats['max'], $decimals),
              'min' => $formatStat($stats['min'], $decimals),
              'avg' => $formatStat($stats['avg'], $decimals),
              'median' => $formatStat($stats['med'], $decimals),
              'mode' => $formatStat($stats['mod'], $decimals),
              'stddev' => $formatStat($stats['sd'], $decimals + 1),
            ],
          )
          : Yii::t(
            'app-statistics',
            'max={max} min={min} average={avg} median={median} stddev={stddev}',
            [
              'max' => $formatStat($stats['max'], $decimals),
              'min' => $formatStat($stats['min'], $decimals),
              'avg' => $formatStat($stats['avg'], $decimals),
              'median' => $formatStat($stats['med'], $decimals),
              'stddev' => $formatStat($stats['sd'], $decimals + 1),
            ],
          ),
      ],
    ),
    ['class' => 'text-right'],
  ) . "\n";
}

echo Html::tag(
  'td',
  Html::encode(match (true) {
    $summary['deaths_for_ratio'] > 0 => $fmt->asDecimal(
      $summary['kills_for_ratio'] / $summary['deaths_for_ratio'],
      2,
    ),
    $summary['kills_for_ratio'] > 0 => $fmt->asDecimal(99.99, 2),
    default => '',
  }),
  ['class' => 'text-right'],
) . "\n";
