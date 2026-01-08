<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Lobby3;
use app\models\LobbyGroup3;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var LobbyGroup3 $lobbyGroup
 * @var View $this
 * @var Rule3|null $rule
 * @var array{lobby_id: int, lobby_group_id: int, rule_id: int, win_unknown: int, win_knockout: int, win_time: int, lose_unknown: int, lose_knockout: int, lose_time: int, total_seconds: int} $stats
 */

if (!$rule) {
  echo $this->render('heading-rule', ['rule' => $rule]);
  return;
}

$canKnockout = !in_array($rule->group?->key, ['nawabari', 'tricolor']);

echo $this->render('heading-rule', ['rule' => $rule]);
echo $this->render('pie-chart', [
  'canKnockout' => $canKnockout,
  'loseKnockout' => $stats['lose_knockout'],
  'loseTime' => $stats['lose_time'],
  'loseUnknown' => $stats['lose_unknown'],
  'winKnockout' => $stats['win_knockout'],
  'winTime' => $stats['win_time'],
  'winUnknown' => $stats['win_unknown'],
]);

if ($canKnockout) {
  echo $this->render('battle-time', [
    'battles' => array_reduce(
      array_map(
        fn (string $key, int $value): int => preg_match('/^(?:win|lose)_/', $key) ? $value : 0,
        array_keys($stats),
        array_values($stats),
      ),
      fn (int $carry, int $value): int => $carry + $value,
      0,
    ),
    'seconds' => $stats['total_seconds'],
  ]);
}
