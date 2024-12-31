<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$percentile = fn($expr, $fraction) => vsprintf('PERCENTILE_CONT(%.2f) WITHIN GROUP (ORDER BY %s)', [
  $fraction,
  $expr,
]);

$inked = sprintf('(CASE %s END)', implode(' ', [
  'WHEN battle2.is_win = TRUE AND battle2.my_point >= 1000 THEN battle2.my_point - 1000',
  'WHEN battle2.is_win = FALSE AND battle2.my_point >= 0 THEN battle2.my_point',
  'ELSE NULL',
]));

$s2v4releasedAt = '2018-09-14T11:00:00+09:00';

$clout = fn($lobby) => sprintf('(CASE %s END)', implode(' ', [
  "WHEN lobby2.key <> '{$lobby}' THEN NULL",
  "WHEN LOWER(splatfest2.term) < '{$s2v4releasedAt}' THEN NULL",
  'ELSE battle2.total_clout_after',
]));

$query = (new Query())
  ->select([
    'fest_id' => 'splatfest2.id',
    'count' => 'COUNT(*)',
    'win' => 'SUM(CASE WHEN battle2.is_win = TRUE THEN 1 ELSE 0 END)',
    'lose' => 'SUM(CASE WHEN battle2.is_win = FALSE THEN 1 ELSE 0 END)',
    'kd_present' => 'SUM(CASE WHEN battle2.kill IS NOT NULL AND battle2.death IS NOT NULL THEN 1 ELSE 0 END)',
    'total_kill' => 'SUM(battle2.kill)',
    'min_kill' => 'MIN(battle2.kill)',
    'max_kill' => 'MAX(battle2.kill)',
    'median_kill' => $percentile('battle2.kill', 0.5),
    'q1_4_kill' => $percentile('battle2.kill', 0.25),
    'q3_4_kill' => $percentile('battle2.kill', 0.75),
    'pct5_kill' => $percentile('battle2.kill', 0.05),
    'pct95_kill' => $percentile('battle2.kill', 0.95),
    'stddev_kill' => 'STDDEV_POP(battle2.kill)',
    'total_death' => 'SUM(battle2.death)',
    'min_death' => 'MIN(battle2.death)',
    'max_death' => 'MAX(battle2.death)',
    'median_death' => $percentile('battle2.death', 0.5),
    'q1_4_death' => $percentile('battle2.death', 0.25),
    'q3_4_death' => $percentile('battle2.death', 0.75),
    'pct5_death' => $percentile('battle2.death', 0.05),
    'pct95_death' => $percentile('battle2.death', 0.95),
    'stddev_death' => 'STDDEV_POP(battle2.death)',
    'inked_present' => "SUM(CASE WHEN ($inked) >= 0 THEN 1 ELSE 0 END)",
    'total_inked' => "SUM($inked)",
    'min_inked' => "MIN($inked)",
    'max_inked' => "MAX($inked)",
    'median_inked' => $percentile($inked, 0.5),
    'q1_4_inked' => $percentile($inked, 0.25),
    'q3_4_inked' => $percentile($inked, 0.75),
    'pct5_inked' => $percentile($inked, 0.05),
    'pct95_inked' => $percentile($inked, 0.95),
    'stddev_inked' => "STDDEV_POP($inked)",
    'is_v4' => "BOOL_OR(LOWER(splatfest2.term) >= '{$s2v4releasedAt}')",
    'clout_normal' => sprintf('MAX(%s)', $clout('fest_normal')),
    'clout_pro' => sprintf('MAX(%s)', $clout('standard')),
    'fest_power_v1' =>  sprintf('MAX(CASE %s END)', implode(' ', [
      "WHEN LOWER(splatfest2.term) >= '{$s2v4releasedAt}' THEN NULL", // skip if v4-
      'WHEN battle2.fest_power IS NULL OR battle2.fest_power < 1 THEN NULL',
      "WHEN lobby2.key IN ('standard', 'squad_4') THEN battle2.fest_power",
      'ELSE NULL',
    ])),
    // 個人のフェスパワーはとれないみたいなので、チームのフェスパワーの平均をとりあえず使う
    'fest_power_v4_normal' => sprintf('AVG(CASE %s END)', implode(' ', [
      "WHEN LOWER(splatfest2.term) < '{$s2v4releasedAt}' THEN NULL", // skip if -v3
      'WHEN battle2.my_team_estimate_fest_power IS NULL OR battle2.my_team_estimate_fest_power < 1 THEN NULL',
      "WHEN lobby2.key = 'fest_normal' THEN battle2.my_team_estimate_fest_power",
      'ELSE NULL',
    ])),
    'fest_power_v4_pro' => sprintf('MAX(CASE %s END)', implode(' ', [
      "WHEN LOWER(splatfest2.term) < '{$s2v4releasedAt}' THEN NULL", // skip if -v3
      'WHEN battle2.fest_power IS NULL OR battle2.fest_power < 1 THEN NULL',
      "WHEN lobby2.key = 'standard' THEN battle2.fest_power",
      'ELSE NULL',
    ])),
  ])
  ->from('battle2')
  ->innerJoin('lobby2', 'battle2.lobby_id = lobby2.id')
  ->innerJoin('mode2', 'battle2.mode_id = mode2.id')
  ->innerJoin('rule2', 'battle2.rule_id = rule2.id')
  ->innerJoin('splatfest2', 'battle2.end_at <@ splatfest2.query_term')
  ->innerJoin('splatfest2_region', 'splatfest2.id = splatfest2_region.fest_id')
  ->andWhere([
    'battle2.user_id' => $user->id,
    'lobby2.key' => ['standard', 'fest_normal', 'squad_4'],
    'mode2.key' => 'fest',
    'rule2.key' => 'nawabari',
    'splatfest2_region.region_id' => $region->id,
  ])
  ->groupBy('splatfest2.id');

$summaries = ArrayHelper::map(
  $query->all(),
  'fest_id',
  fn($row) => (object)$row
);

echo $this->render('//show-v2/splatfest/region-switcher', [
  'input' => $input,
  'region' => $region,
  'regions' => $regions,
  'user' => $user,
]) . "\n";

foreach ($splatfests as $fest) {
  echo $this->render('//show-v2/splatfest/splatfest', [
    'fest' => $fest,
    'input' => $input,
    'region' => $region,
    'user' => $user,
    'summary' => $summaries[$fest->id] ?? null,
  ]) . "\n";
}
