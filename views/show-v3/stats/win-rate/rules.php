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
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Lobby3 $lobby
 * @var LobbyGroup3 $lobbyGroup
 * @var View $this
 * @var array<int, Rule3> $rules
 * @var array{lobby_id: int, lobby_group_id: int, rule_id: int, win_unknown: int, win_knockout: int, win_time: int, lose_unknown: int, lose_knockout: int, lose_time: int, total_seconds: int}[] $stats
 */

echo Html::tag(
  'div',
  implode('', array_map(
    fn (array $statsRow): string => Html::tag(
      'div',
      $this->render('rule', [
        'lobby' => $lobby,
        'lobbyGroup' => $lobbyGroup,
        'rule' => ArrayHelper::getValue($rules, $statsRow['rule_id'], null),
        'stats' => $statsRow,
      ]),
      [
        'class' => [
          'mb-3',
          'col-xs-6',
          'col-md-3',
        ],
      ],
    ),
    $stats,
  )),
  [
    'class' => 'row',
  ]
);
