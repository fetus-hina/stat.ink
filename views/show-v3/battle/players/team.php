<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Ability3;
use app\models\Battle3;
use app\models\Battle3PlayedWith;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use app\models\Splatfest3Theme;
use app\models\TricolorRole3;
use app\models\XMatchingGroup3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $battle
 * @var Splatfest3Theme|null $theme
 * @var TricolorRole3|null $role
 * @var View $this
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $players
 * @var array<string, Ability3> $abilities
 * @var array<string, XMatchingGroup3> $weaponMatchingGroup
 * @var array<string, array<int|string, Battle3PlayedWith>> $playedWith
 * @var bool $isTricolor
 * @var bool $isXmatch
 * @var bool $ourTeam
 * @var bool $useXMatchingRange
 * @var string|null $color
 */

$f = Yii::$app->formatter;

$total = function (string $attrName) use ($players): ?int {
  return array_reduce(
    array_map(
      function (BattlePlayer3|BattleTricolorPlayer3 $model) use ($attrName): ?int {
        $v = filter_var($model->{$attrName} ?? null, FILTER_VALIDATE_INT);
        return is_int($v) ? $v : null;
      },
      $players
    ),
    fn (?int $carry, ?int $item): ?int => ($carry !== null && $item !== null)
      ? $carry + $item
      : null,
    0
  );
};

$totalK = $total('kill');
$totalD = $total('death');

$avgMR = null;
if ($isXmatch && $useXMatchingRange && count($players) > 0) {
  $tmpTotal = array_reduce(
    array_map(
      fn (BattlePlayer3|BattleTricolorPlayer3 $model): ?float => filter_var(
        $model->weapon?->mainweapon?->matching_range,
        FILTER_VALIDATE_FLOAT,
      ),
      $players
    ),
    fn (?float $carry, ?float $item): ?float => ($carry !== null && $item !== null)
      ? $carry + $item
      : null,
    0.0,
  );

  if ($tmpTotal !== null && count($players) > 0) {
    $avgMR = $tmpTotal / count($players);
  }
}

$colorClass = $color ? "bg-{$color}" : null;
if ($colorClass) {
  $this->registerCss(vsprintf('.%s{%s}', [
    $colorClass,
    Html::cssStyleFromArray([
      'background-color' => sprintf('#%s !important', $color),
      'color' => '#fff',
      'text-shadow' => '1px 1px 0 #333',
    ]),
  ]));
}

?>
<?= Html::beginTag(
  'tr',
  [
    'class' => array_filter(['bg-warning', $colorClass]),
  ],
) . "\n" ?>
  <?= Html::tag(
    'th',
    $this->render('team-name', compact('ourTeam', 'role', 'theme')),
    ['colspan' => $isXmatch && !$useXMatchingRange ? '4' : '3'],
  ) . "\n"?>
<?php if ($isXmatch && $useXMatchingRange) { ?>
  <td class="text-right"><?= $f->asDecimal($avgMR, 2) ?></td>
<?php } ?>
  <td class="text-right"><?= $f->asInteger($total('inked')) ?></td>
  <td class="text-right"><?= $f->asInteger($totalK) ?></td>
  <td class="text-right"><?= $f->asInteger($totalD) ?></td>
  <td class="text-right"><?php
    if ($totalK !== null && $totalD !== null) {
      if ($totalD > 0) {
        echo $f->asDecimal($totalK / $totalD, 2);
      } elseif ($totalK > 0) {
        echo $f->asDecimal(99.99, 2);
      } else {
        echo '-';
      }
    }
  ?></td>
  <td class="text-right"><?= $f->asInteger($total('special')) ?></td>
<?php if ($isTricolor) { ?>
  <td class="text-right"><?= $f->asInteger($total('signal')) ?></td>
<?php } ?>
</tr>
<?php
foreach (array_values($players) as $i => $player) {
  echo $this->render('//show-v3/battle/players/player', [
    'abilities' => $abilities,
    'battle' => $battle,
    'colorClass' => $colorClass,
    'isFirst' => $i === 0,
    'isTricolor' => $isTricolor,
    'isXmatch' => $isXmatch,
    'nPlayers' => count($players),
    'playedWith' => $playedWith,
    'player' => $player,
    'useXMatchingRange' => $useXMatchingRange,
    'weaponMatchingGroup' => $weaponMatchingGroup,
  ]) . "\n";
}
