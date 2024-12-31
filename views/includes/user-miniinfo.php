<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\AppLinkAsset;
use app\assets\UserMiniinfoAsset;
use app\components\widgets\ActivityWidget;
use app\components\widgets\Icon;
use app\components\widgets\UserIcon;
use app\models\Rank;
use app\models\User;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var User $user
 */

UserMiniinfoAsset::register($this);
$icons = AppLinkAsset::register($this);

$stat = $user->userStat;

$f = Yii::$app->formatter;
?>
<div id="user-miniinfo" itemscope itemtype="http://schema.org/Person" itemprop="author">
  <div id="user-miniinfo-box">
    <h2>
      <?= Html::a(
        implode('', [
          Html::tag(
            'span',
            UserIcon::widget([
              'user' => $user,
              'options' => [
                'width' => '48',
                'height' => '48',
              ],
            ]),
            ['class' => 'miniinfo-user-icon']
          ),
          Html::tag('span', Html::encode($user->name), [
            'itemprop' => 'name',
            'class' => 'miniinfo-user-name',
          ]),
        ]),
        ['show-user/profile', 'screen_name' => $user->screen_name]
      ) . "\n" ?>
    </h2>
<?php if ($stat): ?>
<?php $boxEx = function (string $label, string $value) : string {
  return Html::tag('div', implode('', [
    Html::tag('div', $label, ['class' => 'user-label']),
    Html::tag('div', $value, ['class' => 'user-number']),
  ]), ['class' => 'col-xs-4']);
} ?>
<?php $box = function (string $label, string $value, ?string $tooltip = null) use ($boxEx) : string {
  return $boxEx(
    Html::tag(
      'span',
      Html::encode(Yii::t('app', $label)),
      ['class' => 'auto-tooltip', 'title' => Yii::t('app', $label)]
    ),
    $tooltip !== null
      ? Html::tag('span', Html::encode($value), ['class' => 'auto-tooltip', 'title' => $tooltip])
      : Html::encode($value)
  );
} ?>
<?php $na = Yii::t('app', 'N/A') ?>
    <div class="row">
      <?= $boxEx(
        Html::tag(
          'span',
          Html::encode(Yii::t('app', 'Battles')),
          ['class' => 'auto-tooltip', 'title' => Yii::t('app', 'Battles')]
        ),
        Html::a(
          Html::encode($f->asInteger($stat->battle_count)),
          ['show/user', 'screen_name' => $user->screen_name]
        )
      ) . "\n" ?>
      <?= $box('Win %', $stat->wp === null ? $na : $f->asPercent($stat->wp / 100, 1)) . "\n" ?>
      <?= $box('24H Win %', $stat->wp_short === null ? $na : $f->asPercent($stat->wp_short / 100, 1)) . "\n" ?>
    </div>
    <div class="row">
      <?= $box(
        'Avg Kills',
        $stat->total_kd_battle_count < 1
          ? $na
          : $f->asDecimal($stat->total_kill / $stat->total_kd_battle_count, 2),
        $stat->total_kd_battle_count < 1
          ? null
          : Yii::t('app', '{number, plural, =1{1 kill} other{# kills}} in {battle, plural, =1{1 battle} other{# battles}}', [
            'number' => $stat->total_kill,
            'battle' => $stat->total_kd_battle_count,
          ])
      ) . "\n" ?>
      <?= $box(
        'Avg Deaths',
        $stat->total_kd_battle_count < 1
          ? $na
          : $f->asDecimal($stat->total_death / $stat->total_kd_battle_count, 2),
        $stat->total_kd_battle_count < 1
          ? null
          : Yii::t('app', '{number, plural, =1{1 death} other{# deaths}} in {battle, plural, =1{1 battle} other{# battles}}', [
            'number' => $stat->total_death,
            'battle' => $stat->total_kd_battle_count,
          ])
      ) . "\n" ?>
      <?= $box(
        'Kill Ratio',
        ($stat->total_kill == 0 && $stat->total_death == 0)
          ? $na
          : ($stat->total_death == 0
            ? '∞'
            : $f->asDecimal($stat->total_kill / $stat->total_death, 2)
          ),
        ($stat->total_kill == 0 && $stat->total_death == 0)
          ? $na
          : vsprintf('%s: %s', [
            Yii::t('app', 'Kill Rate'),
            $f->asPercent($stat->total_kill / ($stat->total_kill + $stat->total_death), 1),
          ])
      ) . "\n" ?>
    </div>
<?php // ナワバリ ?>
    <hr>
    <div class="row">
      <div class="col-xs-12">
        <div class="user-label">
          <?= Html::encode(Yii::t('app-rule', 'Turf War')) . "\n" ?>
        </div>
      </div>
    </div>
    <div class="row">
      <?= $boxEx(
        Html::tag(
          'span',
          Html::encode(Yii::t('app', 'Battles')),
          ['class' => 'auto-tooltip', 'title' => Yii::t('app', 'Battles')]
        ),
        Html::a(
          Html::encode($f->asInteger($stat->nawabari_count)),
          ['show/user', 'screen_name' => $user->screen_name, 'filter' => ['rule' => 'nawabari']]
        )
      ) . "\n" ?>
      <?= $box('Win %', $stat->nawabari_wp === null ? $na : $f->asPercent($stat->nawabari_wp / 100, 1)) . "\n" ?>
      <?= $box(
        'Kill Ratio',
        ($stat->nawabari_kill == 0 && $stat->nawabari_death == 0)
          ? $na
          : ($stat->nawabari_death == 0
            ? '∞'
            : $f->asDecimal($stat->nawabari_kill / $stat->nawabari_death, 2)
          ),
        ($stat->nawabari_kill == 0 && $stat->nawabari_death == 0)
          ? null
          : vsprintf('%s: %s', [
            Yii::t('app', 'Kill Rate'),
            $f->asPercent($stat->nawabari_kill / ($stat->nawabari_kill + $stat->nawabari_death), 1),
          ])
      ) . "\n" ?>
      <?= $box(
        'Total Inked',
        $stat->nawabari_inked < 1
          ? $na
          : ($stat->nawabari_inked >= 1000000
            ? $f->asDecimal($stat->nawabari_inked / 1000000, 2) . 'M'
            : $f->asDecimal($stat->nawabari_inked / 1000, 1) . 'k'
          ),
        $stat->nawabari_inked < 1
          ? null
          : $f->asInteger($stat->nawabari_inked)
      ) . "\n" ?>
      <?= $box(
        'Avg Inked',
        $stat->nawabari_inked < 1 || $stat->nawabari_inked_battle < 1
          ? $na
          : $f->asDecimal($stat->nawabari_inked / $stat->nawabari_inked_battle, 1)
      ) . "\n" ?>
      <?= $box(
        'Max Inked',
        $stat->nawabari_inked_max < 1
          ? $na
          : $f->asInteger($stat->nawabari_inked_max)
      ) . "\n" ?>
    </div>
    <hr>
<?php // ガチ ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="user-label">
          <?= Html::encode(Yii::t('app-rule', 'Ranked Battle')) . "\n" ?>
        </div>
      </div>
    </div>
    <div class="row">
      <?= $boxEx(
        Html::tag(
          'span',
          Html::encode(Yii::t('app', 'Battles')),
          ['class' => 'auto-tooltip', 'title' => Yii::t('app', 'Battles')]
        ),
        Html::a(
          Html::encode($f->asInteger($stat->gachi_count)),
          ['show/user', 'screen_name' => $user->screen_name, 'filter' => ['rule' => '@gachi']]
        )
      ) . "\n" ?>
      <?= $box('Win %', $stat->gachi_wp === null ? $na : $f->asPercent($stat->gachi_wp / 100, 1)) . "\n" ?>
      <?= $box('Peak', $stat->gachi_rank_peak > 0 ? Rank::integerToString($stat->gachi_rank_peak) : $na) . "\n" ?>
      <?= $box(
        'Avg Kills',
        $stat->gachi_kd_battle < 1
          ? $na
          : $f->asDecimal($stat->gachi_kill / $stat->gachi_kd_battle, 2),
        $stat->gachi_kd_battle < 1
          ? null
          : Yii::t('app', '{number, plural, =1{1 kill} other{# kills}} in {battle, plural, =1{1 battle} other{# battles}}', [
            'number' => $stat->gachi_kill,
            'battle' => $stat->gachi_kd_battle,
          ])
      ) . "\n" ?>
      <?= $box(
        'Avg Deaths',
        $stat->gachi_kd_battle < 1
          ? $na
          : $f->asDecimal($stat->gachi_death / $stat->gachi_kd_battle, 2),
        $stat->gachi_kd_battle < 1
          ? null
          : Yii::t('app', '{number, plural, =1{1 death} other{# deaths}} in {battle, plural, =1{1 battle} other{# battles}}', [
            'number' => $stat->gachi_death,
            'battle' => $stat->gachi_kd_battle,
          ])
      ) . "\n" ?>
      <?= $box(
        'Kill Ratio',
        ($stat->gachi_kill == 0 && $stat->gachi_death == 0)
          ? $na
          : ($stat->gachi_death == 0
            ? '∞'
            : $f->asDecimal($stat->gachi_kill / $stat->gachi_death, 2)
          ),
        ($stat->gachi_kill == 0 && $stat->gachi_death == 0)
          ? null
          : vsprintf('%s: %s', [
            Yii::t('app', 'Kill Rate'),
            $f->asPercent($stat->gachi_kill / ($stat->gachi_kill + $stat->gachi_death), 1),
          ])
      ) . "\n" ?>
      <?= $box('Kills/min', $stat->gachi_total_time < 1 ? $na : $f->asDecimal($stat->gachi_kill2 * 60 / $stat->gachi_total_time, 2)) . "\n" ?>
      <?= $box('Deaths/min', $stat->gachi_total_time < 1 ? $na : $f->asDecimal($stat->gachi_death2 * 60 / $stat->gachi_total_time, 2)) . "\n" ?>
    </div>
    <hr>
    <div class="miniinfo-databox">
      <div class="user-label">
        <?= Html::encode(Yii::t('app', 'Activity')) . "\n" ?>
      </div>
      <div class="table-responsive bg-white">
        <?= ActivityWidget::widget([
          'user' => $user,
          'months' => 4,
          'longLabel' => false,
          'size' => 9,
          'only' => 'spl1',
        ]) . "\n" ?>
      </div>
    </div>
    <hr>
    <p class="miniinfo-databox">
<?php $list = [
  [
    'url' => ['show/user-stat-nawabari', 'screen_name' => $user->screen_name],
    'text' => 'Stats (Turf War)',
  ],
  [
    'url' => ['show/user-stat-gachi', 'screen_name' => $user->screen_name],
    'text' => 'Stats (Ranked Battle)',
  ],
  [
    'url' => ['show/user-stat-by-rule', 'screen_name' => $user->screen_name],
    'text' => 'Stats (by Mode)',
  ],
  [
    'url' => ['show/user-stat-by-map', 'screen_name' => $user->screen_name],
    'text' => 'Stats (by Stage)',
  ],
  [
    'url' => ['show/user-stat-by-map-rule', 'screen_name' => $user->screen_name],
    'text' => 'Stats (by Mode and Stage)',
  ],
  [
    'url' => ['show/user-stat-by-map-rule-detail', 'screen_name' => $user->screen_name],
    'text' => 'Details',
    'prefix' => '┗',
  ],
  [
    'url' => ['show/user-stat-by-weapon', 'screen_name' => $user->screen_name],
    'text' => 'Stats (by Weapon)',
  ],
  [
    'url' => ['show/user-stat-by-weapon', 'screen_name' => $user->screen_name],
    'text' => 'Stats (by Weapon)',
  ],
  [
    'url' => ['show/user-stat-vs-weapon', 'screen_name' => $user->screen_name],
    'text' => 'Stats (vs. Weapon)',
  ],
  [
    'url' => ['show/user-stat-cause-of-death', 'screen_name' => $user->screen_name],
    'text' => 'Stats (Cause of Death)',
  ],
  [
    'url' => ['show/user-stat-report', 'screen_name' => $user->screen_name],
    'text' => 'Daily Report',
  ],
] ?>
<?php foreach ($list as $item): ?>
<?php if ($item['prefix'] ?? null): ?>
      <?= Html::tag(
        'span',
        implode(' ', [
          Html::encode($item['prefix']),
          Html::a(
            Html::encode(Yii::t('app', $item['text'])),
            $item['url']
          ),
        ]),
        ['style' => 'margin-left:1em']
      ) . "<br>\n"?>
<?php else: ?>
      <?= Html::a(
        implode(' ', [
          Icon::stats(),
          Html::encode(Yii::t('app', $item['text'])),
        ]),
        $item['url']
      ) . "<br>\n" ?>
<?php endif ?>
<?php endforeach ?>
    </p>
<?php endif // have $stat ?>
<?php if ($user->mainWeapon): ?>
    <div class="miniinfo-databox">
      <?= Html::encode(Yii::t('app', 'Favorite Weapon')) . ":\n" ?>
      <?= Html::encode(Yii::t('app-weapon', $user->mainWeapon->name)) ?><br>
      <?= Html::a(Html::encode(Yii::t('app', 'List')), ['show/user-stat-by-weapon', 'screen_name' => $user->screen_name]) . "\n" ?>
    </div>
<?php endif ?>
    <div class="miniinfo-databox">
<?php if ($user->twitter): ?>
<?php TwitterWebIntentsAsset::register($this) ?>
      <div>
        <?= Icon::twitter() . "\n" ?>
        <?= Html::a(
          Html::encode($user->twitter),
          'https://twitter.com/intent/user?' . http_build_query(['screen_name' => $user->twitter]),
          ['rel' => 'nofollow']
        ) . "\n" ?>
      </div>
<?php endif ?>
<?php if ($user->sw_friend_code): ?>
      <div>
        <span class="fa fa-fw"></span>
        <span style="white-space:nowrap"><?= implode('-', [
          'SW',
          substr($user->sw_friend_code, 0, 4),
          substr($user->sw_friend_code, 4, 4),
          substr($user->sw_friend_code, 8, 4),
        ]) ?></span>
      </div>
<?php endif ?>
    </div>
  </div>
</div>
