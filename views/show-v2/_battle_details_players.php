<?php
use app\assets\AppAsset;
use yii\bootstrap\Html;

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
$this->registerCss('#players .its-me{background:#ffc}');

$isGachi = $battle->isGachi;
$hideRank = true;
$hidePoint = true;
$hasName = false;
$hasKD = false;
$hasRankedInked = false;
if (!$battle->rule || $battle->rule->key !== 'nawabari') {
  $hideRank = false;
}
if (!$battle->rule || ($battle->rule->key === 'nawabari' && (!$battle->lobby || $battle->lobby->key !== 'fest'))) {
  $hidePoint = false;
}

$teams = ($battle->is_win === false)
  ? ['his' => $battle->hisTeamPlayers, 'my' => $battle->myTeamPlayers]
  : ['my' => $battle->myTeamPlayers, 'his' => $battle->hisTeamPlayers];

$bonus = (int)($battle->bonus->bonus ?? 1000);

// check has-KD
foreach ($teams as $team) {
  foreach ($team as $player) {
    if (trim($player->name) !== '' || trim($player->splatnet_id) !== '') {
      $hasName = true;
    }
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
        <th style="width:38px"><span class="fa fa-fw"></span></th>
<?php if ($hasName): ?>
        <th class="col-name"><?= Html::encode(Yii::t('app', 'Name')) ?></th>
<?php endif; ?>
        <th class="col-weapon"><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
        <th class="col-level"><?= Html::encode(Yii::t('app', 'Level')) ?></th>
<?php if (!$hideRank): ?>
        <th class="col-rank"><?= Html::encode(Yii::t('app', 'Rank')) ?></th>
<?php endif; ?>
<?php if (!$hidePoint): ?>
<?php
$this->registerJsFile(
  $assetMgr->getAssetUrl(
    $assetMgr->getBundle(AppAsset::class),
    'battle2-players-point-inked.js'
  ),
  ['depends' => AppAsset::class]
);
?>
        <th class="col-point">
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
<?php endif; ?>
<?php if ($hasRankedInked): ?>
        <th class="col-point">
            <span class="col-point-inked">
              <?= Html::encode(Yii::t('app', 'Inked')) . "\n" ?>
            </span>
          </span>
        </th>
<?php endif; ?>
        <th class="col-kasp"><?= Html::encode(Yii::t('app', 'k+a/sp')) ?></th>
<?php if ($hasKD): ?>
        <th class="col-kd">
          <?= Html::encode(Yii::t('app', 'k')) ?>/<?= Html::encode(Yii::t('app', 'd')) . "\n" ?>
        </th>
        <th class="col-kr auto-tooltip" title="<?= Html::encode(Yii::t('app', 'Kill Ratio')) ?>"><?= Html::encode(Yii::t('app', 'Ratio')) ?></th>
        <th class="col-kr auto-tooltip" title="<?= Html::encode(Yii::t('app', 'Kill Rate')) ?>"><?= Html::encode(Yii::t('app', 'Rate')) ?></th>
<?php endif ?>
      </tr>
    </thead>
    <tbody>
<?php foreach ($teams as $teamKey => $players): ?>
      <?= $this->render('_battle_details_players_team', compact(
        'battle',
        'bonus',
        'players',
        'teamKey',
        'hideRank',
        'hidePoint',
        'hasName',
        'hasKD',
        'hasRankedInked'
      )) . "\n" ?>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
