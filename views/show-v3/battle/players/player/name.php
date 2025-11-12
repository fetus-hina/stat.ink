<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use app\models\Battle3PlayedWith;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use app\models\UnregisteredPlayer3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $battle
 * @var Battle3PlayedWith|null $history
 * @var BattlePlayer3|BattleTricolorPlayer3 $player
 * @var View $this
 */

$f = Yii::$app->formatter;

if ($player->is_crowned || $player->crown) {
  echo Html::tag(
    'div',
    Icon::s3Crown($player->crown ?? 'x'),
    [
      'class' => 'small',
      'style' => [
        'line-height' => '1',
      ],
    ],
  );
}

// TODO: blackout / anonymize
$title = $player->splashtagTitle;
if ($title) {
  echo Html::tag(
    'div',
    Html::encode((string)$title->name),
    ['class' => 'small text-muted'],
  );
}

// Create player name with optional link to stats
$playerNameContent = trim(
  vsprintf('%s %s %s', [
    Icon::s3Species($player->species),
    Html::encode($player->name),
    $player->number !== null
      ? Html::tag(
        'span',
        sprintf('#%s', $player->number),
        ['class' => 'text-muted small'],
      )
      : '',
  ]),
);

// Add link to unregistered player stats if possible
if (!$player->is_me && $player->name && $player->number) {
  $splashtag = $player->name . '#' . $player->number;
  $unregisteredPlayer = UnregisteredPlayer3::findBySplashtagString($splashtag);

  if ($unregisteredPlayer && $unregisteredPlayer->hasSignificantData()) {
    $playerNameContent = Html::a(
      $playerNameContent,
      ['unregistered-player-v3/by-splashtag/' . urlencode($splashtag)],
      [
        'title' => Yii::t('app', 'View stats for {name}', ['name' => $player->name]),
        'class' => 'text-decoration-none',
      ]
    );
  }
}

echo Html::tag('div', $playerNameContent);

if ($history?->count > 1) {
  echo Html::tag(
    'div',
    Html::a(
      vsprintf('%s %s', [
        Icon::playedWithHistory(),
        Html::encode($f->asInteger($history->count)),
      ]),
      ['show-v3/user',
        'screen_name' => $battle->user->screen_name,
        'f' => [
          'played_with' => $history->ref_id,
        ],
      ],
    ),
    ['class' => 'small text-muted text-end text-right'],
  );
}
