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
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3[] $lobbies
 * @var LobbyGroup3 $lobbyGroup
 * @var View $this
 * @var array{lobby_id: int, lobby_group_id: int, rule_id: int, win_unknown: int, win_knockout: int, win_time: int, lose_unknown: int, lose_knockout: int, lose_time: int, total_seconds: int}[] $stats
 */

$rules = ArrayHelper::map(
  Rule3::find()->with(['group'])->orderBy(['rank' => SORT_ASC])->all(),
  'id',
  fn (Rule3 $rule): Rule3 => $rule,
);

echo implode(
  '',
  array_map(
    fn (Lobby3 $lobby): string => $this->render('lobby', [
      'lobby' => $lobby,
      'lobbyGroup' => $lobbyGroup,
      'rules' => $rules,
      'stats' => array_filter(
        $stats,
        fn (array $row): bool => (int)$row['lobby_id'] === (int)$lobby->id,
      ),
    ]),
    $lobbies,
  ),
);
