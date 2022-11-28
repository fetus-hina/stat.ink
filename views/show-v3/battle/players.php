<?php

declare(strict_types=1);

use app\models\Battle3;
use app\models\BattlePlayer3;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Battle3 $model
 * @var View $this
 */

$allPlayers = BattlePlayer3::find()
  ->with(
    ArrayHelper::toFlatten([
      ['splashtagTitle', 'weapon', 'weapon.special', 'weapon.subweapon'],
      \array_map(
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
  ->all();
if (!$allPlayers) {
  return;
}

$filterPlayers = function (array $players, bool $ourTeam): array {
  $players = array_filter(
    $players,
    fn (BattlePlayer3 $p): bool => $p->is_our_team === $ourTeam
  );
  usort(
    $players,
    fn (BattlePlayer3 $a, BattlePlayer3 $b): int => ($a->rank_in_team ?: 5) <=> ($b->rank_in_team ?: 5)
      ?: ($a->kill ?: -1) <=> ($b->kill ?: -1)
      ?: ($a->kill_or_assist ?: -1) <=> ($b->kill_or_assist ?: -1)
      ?: ($a->inked ?: -1) <=> ($b->inked ?: -1)
      ?: $a->id <=> $b->id
  );
  return $players;
};

$result = $model->result;
echo $this->render('//show-v3/battle/players/players', [
  'battle' => $model,
  'ourTeamPlayers' => $filterPlayers($allPlayers, true),
  'theirTeamPlayers' => $filterPlayers($allPlayers, false),
  'ourTeamFirst' => !$result || $result->is_win !== false,
]);
