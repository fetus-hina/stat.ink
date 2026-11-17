<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\LobbyGroup3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var LobbyGroup3|null $lobbyGroup
 * @var LobbyGroup3[] $lobbyGroups
 * @var Rule3 $rule
 * @var Rule3[] $rules
 * @var User $user
 * @var View $this
 */

$item = fn (string $label, array $url, bool $active): string => Html::tag(
  'li',
  Html::a($label, $url),
  [
    'class' => $active ? 'active' : null,
    'role' => 'presentation',
  ],
);

?>
<ul class="nav nav-tabs mb-2">
<?php foreach ($rules as $r) { ?>
  <?= $item(
    implode(' ', array_filter([
      Icon::s3Rule($r),
      Html::encode(Yii::t('app-rule3', $r->short_name)),
    ])),
    ['show-v3/stats-rule-trend', 'screen_name' => $user->screen_name, 'rule' => $r->key],
    $r->key === $rule->key,
  ) . "\n" ?>
<?php } ?>
</ul>
<?php if (count($lobbyGroups) > 1 || $lobbyGroup) { ?>
<ul class="nav nav-pills mb-2">
  <?= $item(
    Html::encode(Yii::t('app-lobby3', 'Except Private')),
    ['show-v3/stats-rule-trend', 'screen_name' => $user->screen_name, 'rule' => $rule->key],
    $lobbyGroup === null,
  ) . "\n" ?>
<?php foreach ($lobbyGroups as $g) { ?>
  <?= $item(
    implode(' ', array_filter([
      Icon::s3Lobby($g),
      Html::encode(Yii::t('app-lobby3', $g->name)),
    ])),
    ['show-v3/stats-rule-trend', 'screen_name' => $user->screen_name, 'rule' => $rule->key, 'lobby' => $g->key],
    $g->key === $lobbyGroup?->key,
  ) . "\n" ?>
<?php } ?>
</ul>
<?php } ?>
