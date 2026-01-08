<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\assets\KillRatioColumnAsset;
use app\components\helpers\WeaponShortener;
use app\components\widgets\EmbedVideo;
use app\components\widgets\KillRatioBadgeWidget;
use app\models\Battle;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Battle $model
 * @var View $this
 */

KillRatioColumnAsset::register($this);
$this->registerJs('jQuery(".kill-ratio").killRatioColumn();');

$f = Yii::$app->formatter;
?>
<?= Html::beginTag('tr', ['class' => 'table-row', 'data-period' => $model->period]) . "\n" ?>
  <td class="nobr">
    <?= Html::a(
      Html::encode(Yii::t('app', 'Detail')),
      ['/show/battle', 'screen_name' => $model->user->screen_name, 'battle' => $model->id],
      ['class' => 'btn btn-primary btn-xs']
    ) . "\n" ?>
<?php if ($model->link_url): ?>
    <?= Html::a(
      Html::tag('span', '', ['class' => [
        'fas',
        'fa-fw',
        EmbedVideo::isSupported($model->link_url) ? 'fa-video' : 'fa-link',
      ]]),
      $model->link_url,
      ['class' => 'btn btn-default btn-xs', 'rel' => 'nofollow']
    ) . "\n" ?>
<?php endif ?>
  </td>
  <td class="cell-lobby"><?= Html::encode($model->lobby ? Yii::t('app-rule', $model->lobby->name) : '?') ?></td>
  <td class="cell-rule"><?= Html::encode($model->rule ? Yii::t('app-rule', $model->rule->name) : '?') ?></td>
  <td class="cell-rule-short"><?=
    Html::tag(
      'span',
      Html::encode($model->rule ? Yii::t('app-rule', $model->rule->short_name) : '?'),
      ['class' => 'auto-tooltip', 'title' => $model->rule ? Yii::t('app-rule', $model->rule->name) : '?']
    )
  ?></td>
  <td class="cell-map"><?= Html::encode($model->map ? Yii::t('app-map', $model->map->name) : '?') ?></td>
  <td class="cell-map-short"><?=
    Html::tag(
      'span',
      Html::encode($model->map ? Yii::t('app-map', $model->map->short_name) : '?'),
      ['class' => 'auto-tooltip', 'title' => $model->map ? Yii::t('app-map', $model->map->name) : '?']
    )
  ?></td>
  <td class="cell-main-weapon"><?=
    $model->weapon
      ? Html::tag(
        'span',
        Html::encode(Yii::t('app-weapon', $model->weapon->name)),
        [
          'class' => 'auto-tooltip',
          'title' => implode(' / ', [
            implode(' ', [
              Yii::t('app', 'Sub:'),
              Yii::t('app-subweapon', $model->weapon->subweapon->name),
            ]),
            implode(' ', [
              Yii::t('app', 'Special:'),
              Yii::t('app-special', $model->weapon->special->name),
            ]),
          ]),
        ]
      )
      : Html::encode('?')
  ?></td>
  <td class="cell-main-weapon-short"><?=
    $model->weapon
      ? Html::tag(
        'span',
        Html::encode(WeaponShortener::makeShorter(Yii::t('app-weapon', $model->weapon->name))),
        [
          'class' => 'auto-tooltip',
          'title' => implode(' / ', [
            implode(' ', [
              Yii::t('app', 'Sub:'),
              Yii::t('app-subweapon', $model->weapon->subweapon->name),
            ]),
            implode(' ', [
              Yii::t('app', 'Special:'),
              Yii::t('app-special', $model->weapon->special->name),
            ]),
          ]),
        ]
      )
      : Html::encode('?')
  ?></td>
  <td class="cell-sub-weapon"><?= Html::encode($model->weapon ? Yii::t('app-subweapon', $model->weapon->subweapon->name) : '?') ?></td>
  <td class="cell-special"><?= Html::encode($model->weapon ? Yii::t('app-special', $model->weapon->special->name) : '?') ?></td>
  <td class="cell-rank"><?= Html::encode(
    $model->rank
      ? implode(' ', [
        Yii::t('app-rank', $model->rank->name),
        $model->rank_exp,
      ])
      : ''
  ) ?></td>
  <td class="cell-rank-after"><?= Html::encode(
    $model->rankAfter
      ? implode(' ', [
        Yii::t('app-rank', $model->rankAfter->name),
        $model->rank_exp_after,
      ])
      : ''
  ) ?></td>
  <td class="cell-level"><?= Html::encode($model->level) ?></td>
  <td class="cell-result"><?= implode('&nbsp;', array_filter([
    $model->is_win === null
      ? Html::encode('?')
      : ($model->is_win
        ? Html::tag('span', Html::encode(Yii::t('app', 'Won')), ['class' => 'label label-success'])
        : Html::tag('span', Html::encode(Yii::t('app', 'Lost')), ['class' => 'label label-danger'])
      ),
    (!$model->isGachi || $model->is_knock_out === null)
      ? null
      : ($model->is_knock_out
        ? Html::tag('span', Html::encode(Yii::t('app', 'K.O.')), ['class' => 'label label-info auto-tooltip', 'title' => Yii::t('app', 'Knockout')])
        : Html::tag('span', Html::encode(Yii::t('app', 'Time')), ['class' => 'label label-warning auto-tooltip', 'title' => Yii::t('app', 'Time is up')])
      ),
  ])) ?></td>
  <td class="cell-kd nobr">
<?php $kd = function ($value, $otherValue) use ($f) : string {
  if ($value === null) {
    return Html::encode('?');
  }

  if ($otherValue !== null && $value >= $otherValue) {
    return Html::tag('strong', Html::encode($f->asInteger($value)));
  }

  return Html::encode($f->asInteger($value));
} ?>
    <?= vsprintf('%s / %s', [
      Html::tag('span', $kd($model->kill, $model->death), ['class' => 'kill']),
      Html::tag('span', $kd($model->death, $model->kill), ['class' => 'death']),
    ]) . "\n" ?>
    <?= KillRatioBadgeWidget::widget(['kill' => $model->kill, 'death' => $model->death]) . "\n" ?>
  </td>
  <?= ($model->kill_ratio === null
    ? Html::tag('td', '', ['class' => 'cell-kill-ratio'])
    : Html::tag(
      'td',
      Html::encode($f->asDecimal($model->kill_ratio, 2)),
      [
        'class' => 'text-right kill-ratio cell-kill-ratio',
        'data-kill-ratio' => $model->kill_ratio,
      ]
    )) . "\n"
  ?>
  <?= ($model->kill_rate === null
    ? Html::tag('td', '', ['class' => 'cell-kill-rate'])
    : Html::tag(
      'td',
      Html::encode($f->asPercent($model->kill_rate, 1)),
      [
        'class' => 'text-right kill-ratio cell-kill-rate',
        'data-kill-ratio' => $model->kill_ratio,
      ]
    )) . "\n"
  ?>
  <td class="cell-point"><?= Html::encode($model->my_point ? ($model->inked ?? '?') : '') ?></td> 
  <td class="cell-rank-in-team"><?= Html::encode($model->rank_in_team) ?></td>
  <td class="cell-datetime">
<?php if ($model->end_at === null): ?>
    <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php else: ?>
<?php $t = new DateTimeImmutable($model->end_at, new DateTimeZone(Yii::$app->timeZone)) ?>
    <?= Html::tag(
      'time',
      Html::encode($f->asDateTime($t, 'short')),
      [
        'datetime' => $t->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM),
      ]
    ) . "\n" ?>
<?php endif ?>
  </td>
  <td class="cell-reltime">
<?php if ($model->end_at === null): ?>
    <?= Html::encode(Yii::t('app', 'N/A')) . "\n" ?>
<?php else: ?>
    <?= Html::tag(
      'time',
      Html::encode($f->asRelativeTime($t, $_SERVER['REQUEST_TIME'] ?? time())),
      [
        'class' => 'auto-tooltip',
        'title' => $f->asDateTime($t, 'medium'),
        'datetime' => $t->setTimeZone(new DateTimeZone('Etc/UTC'))->format(DateTime::ATOM),
      ]
    ) . "\n" ?>
<?php endif ?>
  </td>
</tr>
