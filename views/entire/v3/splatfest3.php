<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\OgpHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Map3;
use app\models\Splatfest3;
use app\models\Splatfest3StatsWeapon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Splatfest3 $splatfest
 * @var Splatfest3StatsWeapon[] $weaponsChallenge
 * @var Splatfest3StatsWeapon[] $weaponsOpen
 * @var Splatfest3[] $festList
 * @var View $this
 * @var array<int, Map3> $stages
 * @var array<string, int> $votes
 * @var array<string, string> $colors
 * @var array<string, string> $names
 * @var array{lobby_id: int, fest_dragon_id: int|null, battles: int}[] $dragonStats
 * @var array{map_id: int, battles: int, attacker_wins: int}[] $tricolorStats
 */

$title = Yii::t('app', 'Splatfest Stats') . ' - ' . Yii::t('db/splatfest3', (string)$splatfest->name);
$this->title = $title . ' | ' . Yii::$app->name;

OgpHelper::default($this, title: $this->title);

?>
<div class="container">
  <?= Html::tag(
    'h1',
    Html::encode(Yii::t('app', 'Splatfest Stats')),
    ['class' => 'mt-0 mb-3'],
  ) . "\n" ?>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <div class="mb-3">
    <?= Html::dropDownList(
        '',
        Url::to(['entire/splatfest3', 'id' => $splatfest->id], true),
        ArrayHelper::map(
            $festList,
            fn (Splatfest3 $fest): string => Url::to(['entire/splatfest3', 'id' => $fest->id], true),
            fn (Splatfest3 $fest): string => Yii::t('db/splatfest3', (string)$fest->name),
        ),
        [
            'class' => 'form-control mb-0',
            'onchange' => 'window.location.href = this.value',
        ],
    ) . "\n" ?>
  </div>

  <h2 class="mt-0 mb-3">
    <?= Html::encode(Yii::t('db/splatfest3', (string)$splatfest->name)) . "\n" ?>
    <small class="text-muted">
      <?= vsprintf('(%s)', [
        implode(' / ', array_map(
          fn (string $name): string => Yii::t('db/splatfest3/team', $name),
          $names,
        )),
      ]) . "\n" ?>
     </small>
  </h2>

  <?= $this->render('splatfest3/vote', compact('colors', 'names', 'votes')) . "\n" ?>
  <?= $this->render('splatfest3/tricolor', compact('stages', 'tricolorStats')) . "\n" ?>
  <?= $this->render('splatfest3/power', compact('splatfest')) . "\n" ?>
  <?= $this->render('splatfest3/dragon', compact('splatfest', 'dragonStats')) . "\n" ?>
  <?= $this->render('splatfest3/weapons', compact('splatfest', 'weaponsChallenge', 'weaponsOpen')) . "\n" ?>
</div>
