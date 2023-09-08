<?php

declare(strict_types=1);

use app\components\helpers\Battle3Helper;
use app\models\Ability3;
use app\models\Battle3;
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

$result = $model->result;
echo $this->render('//show-v3/battle/players/players', [
  'abilities' => $abilities,
  'battle' => $model,
  'ourTeamFirst' => !$result || $result->is_win !== false,
  'ourTeamPlayers' => $filterPlayers($allPlayers, true, 1),
  'theirTeamPlayers' => $filterPlayers($allPlayers, false, 2),
  'thirdTeamPlayers' => $filterPlayers($allPlayers, false, 3),
  'weaponMatchingGroup' => Battle3Helper::getWeaponMatchingGroup($model),
]);
