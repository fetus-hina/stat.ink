<?php

declare(strict_types=1);

use app\actions\salmon\v3\stats\schedule\EventTrait;
use app\components\helpers\OgpHelper;
use app\components\helpers\TypeHelper;
use app\components\widgets\AdWidget;
use app\components\widgets\SalmonUserInfo3;
use app\components\widgets\SnsWidget;
use app\models\Salmon3;
use app\models\Salmon3UserStatsPlayedWith;
use app\models\Salmon3UserStatsSpecial;
use app\models\Salmon3UserStatsWeapon;
use app\models\SalmonBoss3;
use app\models\SalmonEvent3;
use app\models\SalmonKing3;
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
 *
 * @var EventStats $eventStats
 * @var Salmon3UserStatsPlayedWith[] $playerStats
 * @var Salmon3[] $results
 * @var SalmonSchedule3 $schedule
 * @var User $user
 * @var View $this
 * @var array<int, Salmon3UserStatsSpecial> $specialStats
 * @var array<int, Salmon3UserStatsWeapon> $weaponStats
 * @var array<int, SalmonBoss3> $bosses
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonKing3> $kings
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array<int, SalmonWeapon3> $weapons
 * @var array<int, Special3> $specials
 * @var array<int, array{boss_id: int, appearances: int, defeated: int, defeated_by_me: int}> $bossStats
 * @var array<int, array{king_id: int, appearances: int, defeated: int}> $kingStats
 * @var array<string, scalar|null> $stats
 */

$permLink = Url::to(
  ['salmon-v3/stats-stats',
    'screen_name' => $user->screen_name,
  ],
  true,
);
$this->registerLinkTag(['rel' => 'canonical', 'href' => $permLink]);

$fmt = Yii::$app->formatter;
$this->title = implode(
  ' | ',
  [
    Yii::t('app-salmon3', "{name}'s Salmon Stats", ['name' => $user->name]),
    Yii::$app->name,
  ],
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
      <div class="alert alert-info">
        <ul>
          <li>
            <?= Html::encode(
              Yii::t('app-salmon3', 'This statistics does not include {eggstra} or {private}.', [
                'eggstra' => Yii::t('app-salmon3', 'Eggstra Work'),
                'private' => Yii::t('app-salmon3', 'Private Job'),
              ]),
            ) . "\n" ?>
          </li>
          <li>
            <?= Html::encode(
              Yii::t(
                'app',
                'If there are any unsubmitted data, they have not been included in this tally.',
              ),
            ) . "\n" ?>
          </li>
        </ul>
      </div>
<?php if (is_int($played) && $played > 0) { ?>
      <div class="alert alert-warning">
        Under construction...
      </div>

      <?= $this->render('schedule/abstract', [
        'map' => null,
        'schedule' => null,
        'stats' => $stats,
        'user' => $user,
      ]) . "\n" ?>
      <?= $this->render('schedule/kings', compact('kings', 'kingStats', 'user')) . "\n" ?>
      <?= $this->render('schedule/bosses', compact('bosses', 'bossStats', 'user')) . "\n" ?>
      <?= $this->render('stats/specials', compact('specials', 'specialStats', 'user')) . "\n" ?>
      <?= $this->render('schedule/events', [
        'eventStats' => $eventStats,
        'events' => $events,
        'map' => null,
        'tides' => $tides,
        'user' => $user,
      ]) . "\n" ?>
      <div class="alert alert-warning">
        ↑ Per-stage statistics will be added.
      </div>
      <div class="alert alert-warning">
        ↑ Considering specifications for handling Big Run.
      </div>
      <?= $this->render('stats/weapons', compact('weaponStats', 'weapons')) . "\n" ?>
      <?= $this->render('stats/players', compact('playerStats')) . "\n" ?>
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
