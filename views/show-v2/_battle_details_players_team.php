<?php
use yii\bootstrap\Html;

$fmt = Yii::$app->formatter;

$totalK = 0;
$totalD = 0;
$totalP = 0;
$totalKA = 0;
$totalSP = 0;
$totalRatio = '';
$totalRate = '';
foreach ($players as $player) {
  $totalK = ($totalK === null || $player->kill === null) ? null : ($totalK + $player->kill);
  $totalD = ($totalD === null || $player->death === null) ? null : ($totalD + $player->death);
  if ($totalP === null || $player->point === null || $battle->is_win === null) {
    $totalP = null;
  }
  if ((bool)$battle->is_win === ($teamKey === 'my')) { // ボーナスがついているはず
    if ($player->point < $bonus) {
      $totalP = null;
    } else {
      $totalP += $player->point - $bonus;
    }
  } else {
    $totalP += $player->point;
  }
  $totalKA = ($totalKA === null || $player->kill_or_assist === null) ? null : ($totalKA + $player->kill_or_assist);
  $totalSP = ($totalSP === null || $player->special === null) ? null : ($totalSP + $player->special);
}
if ($totalK !== null && $totalD !== null) {
  if ($totalD === 0) {
    if ($totalK === 0) {
      $totalRatio = 'N/A';
      $totalRate = 'N/A';
    } else {
      $totalRatio = $fmt->asDecimal(99.99, 2);
      $totalRate = $fmt->asPercent(1, 2);
    }
  } else {
    $totalRatio = $fmt->asDecimal($totalK / $totalD, 2);
    $totalRate = $fmt->asPercent($totalK / ($totalK + $totalD), 2);
  }
}

// チーム合計
echo Html::tag(
  'tr',
  '  ' . implode("\n  ", [
    Html::tag('th', Html::encode(Yii::t('app', ($teamKey === 'my') ? 'Good Guys' : 'Bad Guys')), ['colspan' => 2]),
    Html::tag('td', ''),
    $hideRank ? '' : Html::tag('td', ''),
    $hidePoint ? '' : Html::tag('td', Html::encode($totalP === null ? '' : $fmt->asInteger($totalP)), ['class' => 'text-right']),
    Html::tag('td', '', ['class' => 'text-center']),
    !$hasKD ? '' : Html::tag('td', Html::encode(sprintf(
      '%s / %s',
      $totalK === null ? '?' : $fmt->asInteger($totalK),
      $totalD === null ? '?' : $fmt->asInteger($totalD)
    )), ['class' => 'text-center']),
    !$hasKD ? '' : Html::tag('td', Html::encode($totalRatio), ['class' => 'text-right']),
    !$hasKD ? '' : Html::tag('td', Html::encode($totalRate), ['class' => 'text-right']),
  ]),
  ['class' => 'bg-' . $teamKey]
) . "\n";
foreach ($players as $i => $player) {
  echo Html::tag(
    'tr',
    '  ' . implode("\n  ", [
      Html::tag('td', $player->is_me ? Html::tag('span', '', ['class' => 'fa fa-fw fa-rotate-90 fa-level-up']) : '', ['class' => ['bg-' . $teamKey, 'text-center']]),
      Html::tag(
        'td',
        $player->weapon
          ? Html::tag('span', Html::encode(Yii::t('app-weapon2', $player->weapon->name)), [
            'class' => 'auto-tooltip',
            'title' => Html::encode(sprintf(
              '%s %s / %s %s',
              Yii::t('app', 'Sub:'),
              Yii::t('app-subweapon2', $player->weapon->subweapon->name ?? '?'),
              Yii::t('app', 'Special:'),
              Yii::t('app-special2', $player->weapon->special->name ?? '?')
            )),
          ])
          : '',
        ['class' => 'col-weapon']
      ),
      Html::tag('td', Html::encode($player->level), ['class' => ['col-level', 'text-right']]),
      $hideRank ? '' : Html::tag('td', Html::encode(Yii::t('app-rank2', $player->rank->name ?? '')), ['class' => ['col-rank', 'text-center']]),
      $hidePoint ? '' : Html::tag('td', Html::encode($fmt->asInteger($player->point)), ['class' => ['col-point', 'text-right']]),
      Html::tag('td', Html::encode(sprintf(
        '%s %s / %s', 
        $player->kill_or_assist === null ? '?' : $fmt->asInteger($player->kill_or_assist),
        $player->kill_or_assist !== null && $player->kill !== null
          ? sprintf('(%s)', $fmt->asInteger($player->kill_or_assist - $player->kill))
          : '',
        $player->special === null ? '?' : $fmt->asInteger($player->special)
      )), ['class' => ['col-kasp', 'text-center']]),
      !$hasKD ? '' : Html::tag('td', Html::encode(sprintf(
        '%s / %s',
        $player->kill === null ? '?' : $fmt->asInteger($player->kill),
        $player->death === null ? '?' : $fmt->asInteger($player->death)
      )), ['class' => ['col-kd', 'text-center']]),
      !$hasKD ? '' : Html::tag('td', Html::encode($player->getFormattedKillRatio()), ['class' => ['col-kd', 'text-right']]),
      !$hasKD ? '' : Html::tag('td', Html::encode($player->getFormattedKillRate()), ['class' => ['col-kd', 'text-right']]),
    ]),
    ['class' => $player->is_me ? 'its-me' : '']
  ) . "\n";
}
