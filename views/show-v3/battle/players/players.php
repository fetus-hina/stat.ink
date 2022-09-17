<?php

declare(strict_types=1);

use app\models\Battle3;
use app\models\BattlePlayer3;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var Battle3 $battle
 * @var BattlePlayer3[] $ourTeamPlayers
 * @var BattlePlayer3[] $theirTeamPlayers
 * @var View $this
 * @var bool $ourTeamFirst
 */

?>
<div class="table-responsive">
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
        <th class="text-nowrap text-center col-special"><?= Html::encode(Yii::t('app', 'Specials')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php if ($ourTeamFirst) { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'ourTeam' => true,
        'players' => $ourTeamPlayers,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'ourTeam' => false,
        'players' => $theirTeamPlayers,
      ]) . "\n" ?>
<?php } else { ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'ourTeam' => false,
        'players' => $theirTeamPlayers,
      ]) . "\n" ?>
      <?= $this->render('//show-v3/battle/players/team', [
        'ourTeam' => true,
        'players' => $ourTeamPlayers,
      ]) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
