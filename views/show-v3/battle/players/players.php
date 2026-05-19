<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\Icon;
use app\models\Abilities3;
use app\models\Battle3;
use app\models\Battle3PlayedWith;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use app\models\XMatchingGroup3;
use app\models\XMatchingGroupVersion3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $battle
 * @var View $this
 * @var XMatchingGroupVersion3|null $weaponMatchingGroupVersion
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $ourTeamPlayers
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $theirTeamPlayers
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $thirdTeamPlayers
 * @var array<string, Ability3> $abilities
 * @var array<string, XMatchingGroup3> $weaponMatchingGroup
 * @var array<string, array<int|string, Battle3PlayedWith>> $playedWith
 * @var bool $ourTeamFirst
 */

TableResponsiveForceAsset::register($this);

$isXmatch = $battle->lobby?->key === 'xmatch';
$isXMR = false;
$isTricolor = $battle->rule?->key === 'tricolor';

if (
  $isXmatch &&
  version_compare($battle->version?->tag ?? '0.0.0', '11.1.0', '>=')
) {
  $isXMR = true;
}

$ourSet = [
  'color' => $battle->our_team_color,
  'role' => $battle->ourTeamRole,
  'theme' => $battle->ourTeamTheme,
  'players' => $ourTeamPlayers,
  'ourTeam' => true,
];
$theirSet = [
  'color' => $battle->their_team_color,
  'role' => $battle->theirTeamRole,
  'theme' => $battle->theirTeamTheme,
  'players' => $theirTeamPlayers,
  'ourTeam' => false,
];
$thirdSet = [
  'color' => $battle->third_team_color,
  'role' => $battle->thirdTeamRole,
  'theme' => $battle->thirdTeamTheme,
  'players' => $thirdTeamPlayers,
  'ourTeam' => false,
];

// Build the display order. For tricolor battles, if there is another team
// on the same side (attacker/defender) as ours, group it next to our team
// so that "our group" stays together and our team is always first within it.
// When no same-side other team exists (i.e. we are the lone defender), keep
// the legacy ordering. Non-tricolor battles are also unaffected.
if ($isTricolor) {
  $ourRoleKey = $battle->ourTeamRole?->key;
  $theirSame = $ourRoleKey !== null && $battle->theirTeamRole?->key === $ourRoleKey;
  $thirdSame = $ourRoleKey !== null && $battle->thirdTeamRole?->key === $ourRoleKey;

  if ($theirSame || $thirdSame) {
    $sameSet = $theirSame ? $theirSet : $thirdSet;
    $oppositeSet = $theirSame ? $thirdSet : $theirSet;
    $displayOrder = $ourTeamFirst
      ? [$ourSet, $sameSet, $oppositeSet]
      : [$oppositeSet, $ourSet, $sameSet];
  } else {
    $displayOrder = $ourTeamFirst
      ? [$ourSet, $theirSet, $thirdSet]
      : [$thirdSet, $theirSet, $ourSet];
  }
} else {
  $displayOrder = $ourTeamFirst
    ? [$ourSet, $theirSet]
    : [$theirSet, $ourSet];
}

?>
<div class="table-responsive table-responsive-force mb-3">
  <table class="table table-bordered mb-0" id="players">
    <thead>
      <tr>
        <th class="text-nowrap text-center" style="width:38px"><span class="fa"></span></th>
        <th class="text-nowrap text-center col-name"><?= Html::encode(Yii::t('app', 'Name')) ?></th>
        <th class="text-nowrap text-center col-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
<?php if ($isXmatch) { ?>
        <th class="text-nowrap text-center col-x">
<?php if ($isXMR) { ?>
          <?= Html::tag(
            'span',
            Icon::s3LobbyX() . Html::encode(' MR'),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app-xmatch3', 'Matching Range'),
            ],
          ) . "\n" ?>
<?php } else { ?>
          <?= Html::tag(
            'span',
            Html::encode('X'),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app-xmatch3', 'X: Match making group'),
            ],
          ) . "\n" ?>
<?php } ?>
<?php } ?>
        <th class="text-nowrap text-center col-inked"><?= Html::encode(Yii::t('app', 'Inked')) ?></th>
        <th class="text-nowrap text-center col-kill"><?= implode(' ', [
          Icon::s3Kill(),
          Html::encode(Yii::t('app', 'k')),
        ]) ?></th>
        <th class="text-nowrap text-center col-death"><?= implode(' ' , [
          Icon::s3Death(),
          Html::encode(Yii::t('app', 'd')),
        ]) ?></th>
        <th class="text-nowrap text-center col-kr"><?= Html::encode(Yii::t('app', 'KR')) ?></th>
        <th class="text-nowrap text-center col-special"><?= Html::encode(Yii::t('app', 'Sp')) ?></th>
<?php if ($isTricolor) { ?>
        <th class="text-nowrap text-center col-signal"><?= Icon::s3Signal() ?></th>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($displayOrder as $set) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'battle' => $battle,
        'color' => $set['color'],
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => $set['ourTeam'],
        'playedWith' => $playedWith,
        'players' => $set['players'],
        'role' => $set['role'],
        'theme' => $set['theme'],
        'useXMatchingRange' => $isXMR,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
<?php if ($isXmatch && !$isXMR && $weaponMatchingGroupVersion) { ?>
<p class="mt-2 mb-3 text-right small">
  [<?= Html::encode(Yii::t('app-xmatch3', 'X: Match making group')) ?>]
<?php if (version_compare($weaponMatchingGroupVersion->minimum_version, '6.0.0', '<')) { ?>
  <?= Yii::t('app', 'Source: {source}', [
    'source' => Html::a(
      vsprintf('%s %s', [
        Icon::twitter(),
        Html::encode('@antariska_spl'),
      ]),
      str_starts_with(Yii::$app->language, 'ja-')
        ? 'https://twitter.com/antariska_spl/status/1610201648378556418'
        : 'https://twitter.com/antariska_spl/status/1610203442114629632',
      [
        'target' => '_blank',
        'rel' => 'noopener',
      ],
    ),
  ]) . "\n" ?>
<?php } else { ?>
  <?= Yii::t('app', 'Source: {source}', [
    'source' => Html::a(
      vsprintf('%s %s', [
        Icon::twitter(),
        Html::encode('@M_ClashBlaster'),
      ]),
      'https://twitter.com/M_ClashBlaster/status/1730117977759224074',
      [
        'target' => '_blank',
        'rel' => 'noopener',
      ],
    ),
  ]) . "\n" ?>
<?php } ?>
</p>
<?php } ?>
