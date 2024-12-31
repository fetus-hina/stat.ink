<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\SnsWidget;
use app\models\Map;
use app\models\Rule;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Map $map
 * @var Rule $rule
 * @var View $this
 * @var array<string, string> $maps
 * @var array<string, string> $rules
 * @var object[] $history
 * @var object[] $weapons
 */

$formatter = Yii::$app->formatter;
$now = (int)($_SERVER['REQUEST_TIME'] ?? time());

$title = implode(' - ', [
  Yii::t('app', 'Stages'),
  Yii::t('app-map', $map->name),
  Yii::t('app-rule', $rule->name),
]);

$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerMetaTag(['name' => 'twitter:card', 'content' => 'summary']);
$this->registerMetaTag(['name' => 'twitter:title', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:description', 'content' => $title]);
$this->registerMetaTag(['name' => 'twitter:site', 'content' => '@stat_ink']);

$this->registerCss(
  'td.range{width:1em;text-align:center;padding-left:0;padding-right:0}' .
  '.progress{margin-bottom:0;min-width:150px}'
);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <?= AdWidget::widget() . "\n" ?>
  <?= SnsWidget::widget() . "\n" ?>

  <p class="form-inline">
    <select class="form-control change-map" disabled>
      <?= implode('', array_map(
        function ($key, $value) use ($map, $rule) : string {
          return Html::tag(
            'option',
            Html::encode($value),
            [
              'value' => Url::to(['stage/map-detail', 'map' => $key, 'rule' => $rule->key]),
              'selected' => $map->key === $key,
            ]
          );
        },
        array_keys($maps),
        array_values($maps)
      )) . "\n" ?>
    </select>
    <select class="form-control change-map" disabled>
      <?= implode('', array_map(
        function ($key, $value) use ($map, $rule) : string {
          return Html::tag(
            'option',
            Html::encode($value),
            [
              'value' => Url::to(['stage/map-detail', 'map' => $map->key, 'rule' => $key]),
              'selected' => $rule->key === $key,
            ]
          );
        },
        array_keys($rules),
        array_values($rules)
      )) . "\n" ?>
    </select>
<?php $this->registerJs(<<<'JS'
(function($){
  "use strict";
  $(function(){
    $('.change-map')
      .change(function(){window.location.href=$(this).val()})
      .prop('disabled',!1)
  })
})(jQuery);
JS
) ?>
  </p>

  <div class="row">
    <div class="col-xs-12 col-md-6">
      <h2 id="weapons">
        <?= Html::encode(Yii::t('app', 'Weapon Trends')) . "\n" ?>
      </h2>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th><?= Html::encode(Yii::t('app', 'Weapon')) ?></th>
              <th><?= Html::encode(Yii::t('app', 'Recent Use %')) ?></th>
            </tr>
          </thead>
          <tbody>
<?php $total = array_sum(array_map(
  function ($_) {
    return $_->battles;
  },
  $weapons
)) ?>
<?php $max = count($weapons) === 0 ? 0 : max(array_map(
  function ($_) {
    return $_->battles;
  },
  $weapons
)) ?>
<?php foreach ($weapons as $_): ?>
            <tr>
              <td><?= Html::encode(Yii::t('app-weapon', $_->weapon->name)) ?></td>
              <td>
<?php if ($max > 0 && $total > 0): ?>
                <div class="progress"><?= Html::tag(
                  'div',
                  Html::encode($formatter->asPercent($_->battles / $total, 2)),
                  [
                    'class' => 'progress-bar',
                    'role' => 'progressbar',
                    'style' => [
                      'width' => ($_->battles * 100 / $max) . '%',
                    ],
                  ]
                ) ?></div>
<?php endif ?>
              </td>
            </tr>
<?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="col-xs-12 col-md-6">
      <h2 id="history">
        <?= Html::encode(Yii::t('app', 'Session History')) . "\n" ?>
      </h2>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th></th>
              <th colspan="3" class="text-center"><?= Html::encode(Yii::t('app', 'Period')) ?></th>
              <th class="text-center"><?= Html::encode(Yii::t('app', 'Interval')) ?></th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($history as $_h): ?>
            <tr>
              <?= Html::tag('td', Html::encode(
                (function (int $start, int $end) use ($now, $formatter) : string {
                  if ($start > $now) {
                    return Yii::t('app', 'Scheduled');
                  } elseif ($end > $now) {
                    return Yii::t('app', 'In session');
                  } else {
                    return $formatter->asRelativeTime($end, $now);
                  }
                })($_h->start, $_h->end)
              )) . "\n" ?>
              <td class="text-right"><?= Html::tag(
                'time',
                Html::encode($formatter->asDatetime($_h->start)),
                ['datetime' => gmdate(DateTime::ATOM, $_h->start)]
              ) ?></td>
              <td class="range">-</td>
              <td class="text-left"><?= Html::tag(
                'time',
                Html::encode($formatter->asDatetime($_h->end)),
                ['datetime' => gmdate(DateTime::ATOM, $_h->end)]
              ) . "\n" ?></td>
              <td class="text-left"><?php
                if ($_h->interval === null) {
                  echo Html::tag('div', '-', ['class' => 'text-center']);
                } elseif ($_h->interval === 0) {
                  echo Html::encode(Yii::t('app', 'Continue'));
                } else {
                  echo Html::encode($formatter->asDuration($_h->interval));
                }
              ?></td>
            </tr>
<?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
