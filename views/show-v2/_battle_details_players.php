<?php

declare(strict_types=1);

use app\assets\AppAsset;
use app\models\Battle2;
use app\models\BattlePlayer2;
use yii\bootstrap\Html;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var Battle2 $battle
 * @var View $this
 */

$assetMgr = Yii::$app->assetManager;
if ($battle->my_team_color_rgb && $battle->his_team_color_rgb) {
  $this->registerCss(implode('', [
    sprintf('#players .bg-my{%s}', Html::cssStyleFromArray([
      'background' => '#' . $battle->my_team_color_rgb,
      'color' => '#fff',
      'text-shadow' => '1px 1px 0 rgba(0,0,0,.8)',
    ])),
    sprintf('#players .bg-his{%s}', Html::cssStyleFromArray([
      'background' => '#' . $battle->his_team_color_rgb,
      'color' => '#fff',
      'text-shadow' => '1px 1px 0 rgba(0,0,0,.8)',
    ])),
  ]));
}

$isGachi = $battle->isGachi;
$hideRank = true;
$hidePoint = true;
$hasKD = false;
$hasRankedInked = false;
if (!$battle->rule || $battle->rule->key !== 'nawabari') {
  $hideRank = false;
}
if (
  !$battle->rule ||
  ($battle->rule->key === 'nawabari' && (!$battle->lobby || $battle->lobby->key !== 'fest'))
) {
  $hidePoint = false;
}

$teams = ($battle->is_win === false)
  ? ['his' => $battle->hisTeamPlayers, 'my' => $battle->myTeamPlayers]
  : ['my' => $battle->myTeamPlayers, 'his' => $battle->hisTeamPlayers];

$playerIds = ArrayHelper::getColumn(
  array_filter(
    array_merge($battle->myTeamPlayers, $battle->hisTeamPlayers),
    function (BattlePlayer2 $player): bool {
      return !$player->is_me &&
        is_string($player->splatnet_id) &&
        preg_match('/^[0-9a-f]{16}$/', $player->splatnet_id);
    }
  ),
  'splatnet_id'
);

$historyCount = ArrayHelper::map(
  (new Query())
    ->select([
      '{{battle_player2}}.[[splatnet_id]]',
      'count' => 'COUNT(*)',
    ])
    ->from('battle2')
    ->innerJoin('battle_player2', '{{battle2}}.[[id]] = {{battle_player2}}.[[battle_id]]')
    ->andWhere([
      '{{battle2}}.[[user_id]]' => $battle->user_id,
      '{{battle_player2}}.[[splatnet_id]]' => $playerIds,
    ])
    ->groupBy(['{{battle_player2}}.[[splatnet_id]]'])
    ->all(),
  'splatnet_id',
  'count'
);

$bonus = (int)($battle->bonus->bonus ?? 1000);

// check has-KD
foreach ($teams as $team) {
  foreach ($team as $player) {
    if ($player->kill !== null || $player->death !== null) {
      $hasKD = true;
    }
    if ($hidePoint && $isGachi) {
      if ($player->point > 0) {
        $hasRankedInked = true;
      }
    }
  }
}
?>
<div class="table-responsive">
  <table class="table table-bordered" id="players">
    <thead>
      <tr>
        <th class="text-nowrap" style="width:38px"><span class="fa fa-fw"></span></th>
        <th class="text-nowrap col-name"><?= Html::encode(Yii::t('app', 'Name')) ?></th>
        <th class="text-nowrap col-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
        <th class="text-nowrap col-level"><?= Html::encode(Yii::t('app', 'Level')) ?></th>
<?php if (!$hideRank) { ?>
        <th class="text-nowrap col-rank"><?= Html::encode(Yii::t('app', 'Rank')) ?></th>
<?php } ?>
<?php if (!$hidePoint) { ?>
<?php
$this->registerJsFile(
  $assetMgr->getAssetUrl(
    $assetMgr->getBundle(AppAsset::class),
    'battle2-players-point-inked.js'
  ),
  ['depends' => AppAsset::class]
);
?>
        <th class="text-nowrap col-point">
          <button id="players-swith-point-inked" class="btn btn-default btn-xs pull-right" disabled>
            <span class="fa fa-tint"></span>
          </button>
          <span class="pull-right">
            <span class="col-point-point">
              <?= Html::encode(Yii::t('app', 'Points')) . "\n" ?>
            </span>
            <span class="col-point-inked hidden" aria-hidden="true">
              <?= Html::encode(Yii::t('app', 'Turf Inked')) . "\n" ?>
            </span>
          </span>
        </th>
<?php } ?>
<?php if ($hasRankedInked) { ?>
        <th class="text-nowrap col-point">
            <span class="col-point-inked">
              <?= Html::encode(Yii::t('app', 'Inked')) . "\n" ?>
            </span>
          </span>
        </th>
<?php } ?>
        <th class="text-nowrap col-kasp"><?= Html::encode(Yii::t('app', 'k+a/sp')) ?></th>
<?php if ($hasKD) { ?>
        <th class="text-nowrap col-kd">
          <?= Html::encode(Yii::t('app', 'k')) ?>/<?= Html::encode(Yii::t('app', 'd')) . "\n" ?>
        </th>
        <th class="text-nowrap col-kr">
          <?= implode(Html::tag('br'), [
            Html::tag('span', Html::encode(Yii::t('app', 'Ratio')), [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', 'Kill Ratio'),
            ]) . '/',
            Html::tag('span', Html::encode(Yii::t('app', 'Rate')), [
              'class' => 'auto-tooltip',
              'title' => Yii::t('app', 'Kill Rate'),
            ]),
          ]) . "\n" ?>
<?php } ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($teams as $teamKey => $players) { ?>
      <?= $this->render('_battle_details_players_team', compact(
        'battle',
        'bonus',
        'players',
        'teamKey',
        'hideRank',
        'hidePoint',
        'hasKD',
        'hasRankedInked',
        'historyCount',
      )) . "\n" ?>
<?php } ?>
    </tbody>
  </table>
</div>
