<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\Battle3Helper;
use app\models\Ability3;
use app\models\Battle3;
use app\models\Battle3PlayedWith;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Battle3 $model
 * @var View $this
 */

$allPlayers = match ($model->rule?->key) {
  'tricolor' => BattleTricolorPlayer3::find()
    ->with(
      ArrayHelper::toFlatten([
        [
            'crown',
            'species',
            'splashtagTitle',
            'weapon',
            'weapon.special',
            'weapon.subweapon',
            'weapon.weapon3Aliases',
        ],
        array_map(
          fn (string $base): array => [
            "{$base}",
            "{$base}.ability",
            "{$base}.gearConfigurationSecondary3s",
            "{$base}.gearConfigurationSecondary3s.ability",
          ],
          ['clothing', 'headgear', 'shoes'],
        ),
      ])
    )
    ->andWhere(['battle_id' => $model->id])
    ->orderBy(['id' => SORT_ASC])
    ->all(),
  default => BattlePlayer3::find()
    ->with(
      ArrayHelper::toFlatten([
        [
            'crown',
            'species',
            'splashtagTitle',
            'weapon',
            'weapon.special',
            'weapon.subweapon',
            'weapon.weapon3Aliases',
        ],
        array_map(
          fn (string $base): array => [
            "{$base}",
            "{$base}.ability",
            "{$base}.gearConfigurationSecondary3s",
            "{$base}.gearConfigurationSecondary3s.ability",
          ],
          ['clothing', 'headgear', 'shoes'],
        ),
      ])
    )
    ->andWhere(['battle_id' => $model->id])
    ->orderBy(['id' => SORT_ASC])
    ->all(),
};

if (!$allPlayers) {
  return;
}

$filterPlayers = function (array $players, bool $ourTeam, int $team): array {
  return ArrayHelper::sort(
    array_filter(
      $players,
      fn (BattlePlayer3|BattleTricolorPlayer3 $p): bool => match (true) {
        $p instanceof BattlePlayer3 => $p->is_our_team === $ourTeam,
        $p instanceof BattleTricolorPlayer3 => $p->team === $team,
        default => throw new LogicException(),
      },
    ),
    function (BattlePlayer3|BattleTricolorPlayer3 $a, BattlePlayer3|BattleTricolorPlayer3 $b): int {
      return ($a->rank_in_team ?: 5) <=> ($b->rank_in_team ?: 5)
        ?: ($a->kill ?: -1) <=> ($b->kill ?: -1)
        ?: ($a->kill_or_assist ?: -1) <=> ($b->kill_or_assist ?: -1)
        ?: ($a->inked ?: -1) <=> ($b->inked ?: -1)
        ?: $a->id <=> $b->id;
    },
  );
};

$abilities = ArrayHelper::map(
  Ability3::find()->orderBy(['rank' => SORT_ASC])->all(),
  'key',
  fn (Ability3 $v): Ability3 => $v,
);

$tmpCount = count(
  array_filter(
    $allPlayers,
    fn (BattlePlayer3|BattleTricolorPlayer3 $p): bool => !$p->is_me &&
      $p->name !== null &&
      $p->number !== null,
  ),
);

/**
 * @var array<string, array<int|string, Battle3PlayedWith>> $playedWith
 */
$playedWith = [];
if ($tmpCount > 0) {
  $playedWith = ArrayHelper::index(
    Battle3PlayedWith::find()
      ->andWhere(['user_id' => $model->user_id])
      ->andWhere(
        array_merge(
          ['or'],
          array_map(
            fn (BattlePlayer3|BattleTricolorPlayer3 $p): array => [
              'name' => $p->name,
              'number' => $p->number,
            ],
            array_filter(
              $allPlayers,
              fn (BattlePlayer3|BattleTricolorPlayer3 $p): bool => !$p->is_me &&
                $p->name !== null &&
                $p->number !== null,
            ),
          ),
        ),
      )
      ->limit(7)
      ->all(),
    'number',
    'name',
  );
}
unset($tmpCount);

$result = $model->result;
echo $this->render('//show-v3/battle/players/players', [
  'abilities' => $abilities,
  'battle' => $model,
  'ourTeamFirst' => !$result || $result->is_win !== false,
  'ourTeamPlayers' => $filterPlayers($allPlayers, true, 1),
  'playedWith' => $playedWith,
  'theirTeamPlayers' => $filterPlayers($allPlayers, false, 2),
  'thirdTeamPlayers' => $filterPlayers($allPlayers, false, 3),
  'weaponMatchingGroup' => Battle3Helper::getWeaponMatchingGroup($model),
  'weaponMatchingGroupVersion' => Battle3Helper::getWeaponMatchingGroupVersion($model),
]);
