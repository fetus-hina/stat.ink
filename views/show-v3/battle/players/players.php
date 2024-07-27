<?php

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
$isTricolor = $battle->rule?->key === 'tricolor';

?>
<div class="table-responsive table-responsive-force mb-3">
  <table class="table table-bordered mb-0" id="players">
    <thead>
      <tr>
        <th class="text-nowrap text-center" style="width:38px"><span class="fa fa-fw"></span></th>
        <th class="text-nowrap text-center col-name"><?= Html::encode(Yii::t('app', 'Name')) ?></th>
        <th class="text-nowrap text-center col-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
<?php if ($isXmatch) { ?>
        <th class="text-nowrap text-center col-x">
          <?= Html::tag(
            'span',
            Html::encode('X'),
            [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app-xmatch3', 'X: Match making group'),
            ],
          ) . "\n" ?>
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
<?php if ($ourTeamFirst) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->our_team_color,
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => true,
        'playedWith' => $playedWith,
        'players' => $ourTeamPlayers,
        'role' => $battle->ourTeamRole,
        'theme' => $battle->ourTeamTheme,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->their_team_color,
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => false,
        'playedWith' => $playedWith,
        'players' => $theirTeamPlayers,
        'role' => $battle->theirTeamRole,
        'theme' => $battle->theirTeamTheme,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
<?php if ($isTricolor) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->third_team_color,
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => false,
        'playedWith' => $playedWith,
        'players' => $thirdTeamPlayers,
        'role' => $battle->thirdTeamRole,
        'theme' => $battle->thirdTeamTheme,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
<?php } ?>
<?php } else { ?>
<?php if ($isTricolor) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->third_team_color,
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => true,
        'playedWith' => $playedWith,
        'players' => $thirdTeamPlayers,
        'role' => $battle->thirdTeamRole,
        'theme' => $battle->thirdTeamTheme,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
<?php } ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->their_team_color,
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => false,
        'playedWith' => $playedWith,
        'players' => $theirTeamPlayers,
        'role' => $battle->theirTeamRole,
        'theme' => $battle->theirTeamTheme,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->our_team_color,
        'isTricolor' => $isTricolor,
        'isXmatch' => $isXmatch,
        'ourTeam' => true,
        'playedWith' => $playedWith,
        'players' => $ourTeamPlayers,
        'role' => $battle->ourTeamRole,
        'theme' => $battle->ourTeamTheme,
        'weaponMatchingGroup' => $weaponMatchingGroup,
      ]) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
<?php if ($isXmatch && $weaponMatchingGroupVersion) { ?>
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
