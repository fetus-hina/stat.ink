<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\assets\TableResponsiveForceAsset;
use app\models\Abilities3;
use app\models\Battle3;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $battle
 * @var View $this
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $ourTeamPlayers
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $theirTeamPlayers
 * @var array<BattlePlayer3|BattleTricolorPlayer3> $thirdTeamPlayers
 * @var array<string, Ability3> $abilities
 * @var bool $ourTeamFirst
 */

TableResponsiveForceAsset::register($this);

$isTricolor = $battle->rule?->key === 'tricolor';

?>
<div class="table-responsive table-responsive-force">
  <table class="table table-bordered" id="players">
    <thead>
      <tr>
        <th class="text-nowrap text-center" style="width:38px"><span class="fa fa-fw"></span></th>
        <th class="text-nowrap text-center col-name"><?= Html::encode(Yii::t('app', 'Name')) ?></th>
        <th class="text-nowrap text-center col-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
        <th class="text-nowrap text-center col-inked"><?= Html::encode(Yii::t('app', 'Inked')) ?></th>
        <th class="text-nowrap text-center col-kill"><?= Html::encode(Yii::t('app', 'k')) ?></th>
        <th class="text-nowrap text-center col-death"><?= Html::encode(Yii::t('app', 'd')) ?></th>
        <th class="text-nowrap text-center col-kr"><?= Html::encode(Yii::t('app', 'KR')) ?></th>
        <th class="text-nowrap text-center col-special"><?= Html::encode(Yii::t('app', 'Sp')) ?></th>
<?php if ($isTricolor) { ?>
        <th class="text-nowrap text-center col-signal"><?=
          Html::img(
            Yii::$app->assetManager->getAssetUrl(GameModeIconsAsset::register($this), 'spl3/tricolor-attacker.png'),
            [
              'class' => 'auto-tooltip basic-icon',
              'title' => Yii::t('app', 'Try to secure the Ultra Signal'),
            ],
          )
        ?></th>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php if ($ourTeamFirst) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->our_team_color,
        'isTricolor' => $isTricolor,
        'ourTeam' => true,
        'players' => $ourTeamPlayers,
        'role' => $battle->ourTeamRole,
        'theme' => $battle->ourTeamTheme,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->their_team_color,
        'isTricolor' => $isTricolor,
        'ourTeam' => false,
        'players' => $theirTeamPlayers,
        'role' => $battle->theirTeamRole,
        'theme' => $battle->theirTeamTheme,
      ]) . "\n" ?>
<?php if ($isTricolor) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->third_team_color,
        'isTricolor' => $isTricolor,
        'ourTeam' => false,
        'players' => $thirdTeamPlayers,
        'role' => $battle->thirdTeamRole,
        'theme' => $battle->thirdTeamTheme,
      ]) . "\n" ?>
<?php } ?>
<?php } else { ?>
<?php if ($isTricolor) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->third_team_color,
        'isTricolor' => $isTricolor,
        'ourTeam' => true,
        'players' => $thirdTeamPlayers,
        'role' => $battle->thirdTeamRole,
        'theme' => $battle->thirdTeamTheme,
      ]) . "\n" ?>
<?php } ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->their_team_color,
        'isTricolor' => $isTricolor,
        'ourTeam' => false,
        'players' => $theirTeamPlayers,
        'role' => $battle->theirTeamRole,
        'theme' => $battle->theirTeamTheme,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->our_team_color,
        'isTricolor' => $isTricolor,
        'ourTeam' => true,
        'players' => $ourTeamPlayers,
        'role' => $battle->ourTeamRole,
        'theme' => $battle->ourTeamTheme,
      ]) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
