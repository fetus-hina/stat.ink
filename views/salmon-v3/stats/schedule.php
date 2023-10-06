<?php

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\EventTrait;
use app\actions\salmon\v3\stats\schedule\OverfishingTrait;
use app\actions\salmon\v3\stats\schedule\PlayerTrait;
use app\actions\salmon\v3\stats\schedule\WeaponTrait;
use app\components\helpers\OgpHelper;
use app\components\helpers\TypeHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo3;
use app\components\widgets\SnsWidget;
use app\models\Map3;
use app\models\Salmon3;
use app\models\SalmonBoss3;
use app\models\SalmonEvent3;
use app\models\SalmonKing3;
use app\models\SalmonMap3;
use app\models\SalmonSchedule3;
use app\models\SalmonWaterLevel2;
use app\models\SalmonWeapon3;
use app\models\Special3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @phpstan-import-type EventStats from EventTrait
 * @phpstan-import-type OverfishingStats from OverfishingTrait
 * @phpstan-import-type PlayerStats from PlayerTrait
 * @phpstan-import-type WeaponStats from WeaponTrait
 *
 * @var EventStats $eventStats
 * @var Map3|SalmonMap3|null $map
 * @var OverfishingStats|null $overfishing
 * @var PlayerStats[] $playerStats
 * @var Salmon3[] $results
 * @var SalmonSchedule3 $schedule
 * @var User $user
 * @var View $this
 * @var array<int, SalmonBoss3> $bosses
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonKing3> $kings
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array<int, SalmonWeapon3> $weapons
 * @var array<int, Special3> $specials
 * @var array<int, WeaponStats> $weaponStats
 * @var array<int, array> $specialStats
 * @var array<int, array{boss_id: int, appearances: int, defeated: int, defeated_by_me: int}> $bossStats
 * @var array<int, array{king_id: int, appearances: int, defeated: int}> $kingStats
 * @var array<string, scalar|null> $stats
 */

$permLink = Url::to(
  ['salmon-v3/stats-schedule',
    'screen_name' => $user->screen_name,
    'schedule' => $schedule->id,
  ],
  true,
);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);

$fmt = Yii::$app->formatter;
$this->title = implode(
  ' | ',
  array_filter(
    [
      Yii::t('app-salmon3', "{name}'s Salmon Stats", ['name' => $user->name]),
      $map
        ? Yii::t('app-map3', $map->name)
        : null,
      Yii::t('app', '{from} - {to}', [
        'from' => $fmt->asDateTime($schedule->start_at, 'short'),
        'to' => $fmt->asDateTime($schedule->end_at, 'short'),
      ]),
      Yii::$app->name,
    ],
    fn (?string $t): bool => $t !== null,
  ),
);

OgpHelper::profileV3(
  view: $this,
  user: $user,
  url: $permLink,
  description: Yii::t('app-salmon3', "{name}'s Salmon Stats", ['name' => $user->name]),
);

$played = TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'count'));

?>
<div class="container">
  <h1>
    <?= Html::encode(
      Yii::t('app-salmon3', "{name}'s Salmon Stats", ['name' => $user->name]),
    ) . "\n" ?>
  </h1>

  <?= SnsWidget::widget([]) . "\n" ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-lg-9 mb-3">
      <h2>
        <?= Html::encode(Yii::t('app-map3', $map?->name ?? '?')) . "\n" ?>
      </h2>
      <p class="small text-muted">
        <?= Html::encode(
          Yii::t('app', '{from} - {to}', [
            'from' => $fmt->asDateTime($schedule->start_at, 'medium'),
            'to' => $fmt->asDateTime($schedule->end_at, 'medium'),
          ]),
        ) . "\n" ?>
      </p>
<?php if (is_int($played) && $played > 0) { ?>
      <div class="alert alert-warning">
        Under construction...
      </div>

      <?= $this->render('schedule/abstract', compact(
        'events',
        'map',
        'overfishing',
        'schedule',
        'stats',
        'tides',
        'user',
      )) . "\n" ?>
      <?= $this->render('schedule/kings', compact('kings', 'kingStats', 'user')) . "\n" ?>
      <?= $this->render('schedule/bosses', compact('bosses', 'bossStats', 'user')) . "\n" ?>
      <?= $this->render('schedule/specials', compact('specials', 'specialStats', 'user')) . "\n" ?>
      <?= $this->render('schedule/events', compact('eventStats', 'events', 'map', 'tides', 'user')) . "\n" ?>
      <?= $this->render('schedule/weapons', compact('schedule', 'weaponStats', 'weapons')) . "\n" ?>
      <?= $this->render('schedule/players', compact('playerStats')) . "\n" ?>
<?php } else { ?>
      <p>
        <?= Html::encode(Yii::t('app', 'No Data')) . "\n" ?>
      </p>
<?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 col-lg-3 mb-3">
      <?= SalmonUserInfo3::widget(['user' => $user]) . "\n" ?>
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>
