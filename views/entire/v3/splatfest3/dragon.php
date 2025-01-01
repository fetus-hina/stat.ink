<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\DragonMatch3;
use app\models\Lobby3;
use app\models\Splatfest3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Splatfest3 $splatfest
 * @var View $this
 * @var array{lobby_id: int, fest_dragon_id: int|null, battles: int}[] $dragonStats
 */

$totalSamples = array_sum(ArrayHelper::getColumn($dragonStats, 'battles'));
if ($totalSamples < 100) {
  $totalSamples = 0;
}

$lobbies = [];
$dragons = [];
if ($totalSamples) {
  $lobbies = Lobby3::find()
    ->andWhere([
      'key' => [
        'splatfest_open',
        'splatfest_challenge',
      ],
    ])
    ->orderBy([
      'rank' => SORT_ASC,
    ])
    ->cache(86400)
    ->all();

  $dragons = ArrayHelper::map(
    DragonMatch3::find()->cache(86400)->all(),
    'key',
    'id',
  );
}

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <h2 class="panel-title">
      <?= implode(', ', [
        Html::encode(Yii::t('app', '10x Battle')),
        Html::encode(Yii::t('app', '100x Battle')),
        Html::encode(Yii::t('app', '333x Battle')),
      ]) . "\n" ?>
    </h2>
  </div>
  <div class="panel-body pb-0">
<?php if ($totalSamples > 100) { ?>
    <div class="table-responsive mb-3">
      <table class="table table-bordered table-striped table-condensed w-auto mb-0">
        <thead>
          <tr>
            <th></th>
            <th class="text-center"><?= Html::encode(Yii::t('app', 'Samples')) ?></th>
            <th class="text-center"><?= Html::encode(Yii::t('app', '1x Battle')) ?></th>
            <th class="text-center"><?= Html::encode(Yii::t('app', '10x Battle')) ?></th>
            <th class="text-center"><?= Html::encode(Yii::t('app', '100x Battle')) ?></th>
            <th class="text-center"><?= Html::encode(Yii::t('app', '333x Battle')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?= implode('', array_map(
            fn (Lobby3 $lobby) => $this->render('dragon/row', [
              'lobby' => $lobby,
              'dragonStats' => $dragonStats,
              'dragons' => $dragons,
            ]),
            $lobbies,
          )) . "\n" ?>
        </tbody>
      </table>
    </div>
<?php } else { ?>
    <p class="mb-3 text-muted">
      <?= Html::encode(Yii::t('app', 'Not enough data is available.')) . "\n" ?>
    </p>
<?php } ?>
  </div>
</div>
