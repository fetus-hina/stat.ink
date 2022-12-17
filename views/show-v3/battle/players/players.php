<?php

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\models\Abilities3;
use app\models\Battle3;
use app\models\BattlePlayer3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle3 $battle
 * @var BattlePlayer3[] $ourTeamPlayers
 * @var BattlePlayer3[] $theirTeamPlayers
 * @var View $this
 * @var array<string, Ability3> $abilities
 * @var bool $ourTeamFirst
 */

TableResponsiveForceAsset::register($this);

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
      </tr>
    </thead>
    <tbody>
<?php if ($ourTeamFirst) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->our_team_color,
        'ourTeam' => true,
        'players' => $ourTeamPlayers,
        'role' => $battle->ourTeamRole,
        'theme' => $battle->ourTeamTheme,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->their_team_color,
        'ourTeam' => false,
        'players' => $theirTeamPlayers,
        'role' => $battle->theirTeamRole,
        'theme' => $battle->theirTeamTheme,
      ]) . "\n" ?>
<?php } else { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->their_team_color,
        'ourTeam' => false,
        'players' => $theirTeamPlayers,
        'role' => $battle->theirTeamRole,
        'theme' => $battle->theirTeamTheme,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'abilities' => $abilities,
        'color' => $battle->our_team_color,
        'ourTeam' => true,
        'players' => $ourTeamPlayers,
        'role' => $battle->ourTeamRole,
        'theme' => $battle->ourTeamTheme,
      ]) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
