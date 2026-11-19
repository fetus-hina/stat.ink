<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\show\v3\stats\BadgeAction;
use app\assets\s3PixelIcons\LobbyIconAsset;
use app\components\helpers\Battle3SeriesWin;
use app\models\UserBadge3SeriesWin;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var BadgeAction::ORDER_* $order
 * @var View $this
 * @var array<string, UserBadge3SeriesWin> $badgeSeriesWins
 * @var array<string, int> $badgeAdjust
 * @var bool $isEditing
 */

$am = Yii::$app->assetManager;
$icon = LobbyIconAsset::register($this);

$items = [
  [
    'lobby' => Battle3SeriesWin::LOBBY_BANKARA_CHALLENGE,
    'icon' => 'bankara.png',
    'label' => Yii::t('app-lobby3', 'Anarchy Battle (Series)'),
    'steps' => [
      [  0,  10, 0, 1],
      [ 10,  50, 1, 2],
      [ 50, 250, 2, 3],
      [250, null, 3, 3],
    ],
  ],
  [
    'lobby' => Battle3SeriesWin::LOBBY_XMATCH,
    'icon' => 'xmatch.png',
    'label' => Yii::t('app-show-v3', '{lobby} (X Power {xPower} or higher)', [
      'lobby' => Yii::t('app-lobby3', 'X Battle'),
      'xPower' => (string)Battle3SeriesWin::XMATCH_MIN_X_POWER,
    ]),
    'steps' => [
      [  0,  15, 0, 1],
      [ 15,  70, 1, 2],
      [ 70, 350, 2, 3],
      [350, null, 3, 3],
    ],
  ],
];

// reorder $items for display
if ($order === BadgeAction::ORDER_NUMBER) {
  $countOf = fn (array $item): int => array_sum([
    (int)ArrayHelper::getValue($badgeSeriesWins, [$item['lobby'], 'count'], 0),
    (int)ArrayHelper::getValue($badgeAdjust, "series-{$item['lobby']}", 0),
  ]);

  usort($items, fn (array $a, array $b): int => $countOf($b) <=> $countOf($a));
}

echo $this->render('includes/group-header', ['label' => Yii::t('app-show-v3', 'Series Wins')]);
foreach ($items as $item) {
  $key = 'series-' . $item['lobby'];
  echo $this->render('includes/row', [
    'isEditing' => $isEditing,
    'itemKey' => $key,
    'icon' => $am->getAssetUrl($icon, $item['icon']),
    'label' => $item['label'],
    'value' => ArrayHelper::getValue($badgeSeriesWins, [$item['lobby'], 'count']),
    'adjust' => (int)ArrayHelper::getValue($badgeAdjust, $key, 0),
    'badgePath' => 'series/' . $item['lobby'],
    'steps' => $item['steps'],
  ]);
}
